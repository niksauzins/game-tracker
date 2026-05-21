<?php
session_start();
require_once '../config/db.php';
requireLogin();

// Get entry id from url
$entry_id = intval($_GET['id'] ?? 0);

// Get the single entry from database
$stmt = $conn->prepare('
    SELECT ge.*, g.title, g.description, g.genre, g.release_year, g.image_url
    FROM game_entries ge
    INNER JOIN games g ON ge.game_id = g.id
    WHERE ge.id = ? AND ge.user_id = ?
');
$stmt->bind_param('ii', $entry_id, $_SESSION['user_id']);
$stmt->execute();
$entry = $stmt->get_result()->fetch_assoc();

// If no game found, go back
if (!$entry) {
    header('Location: ../pages/entries.php?error=entry_not_found');
    exit;
}

// Get all sessions for this entry
$stmt = $conn->prepare('SELECT * FROM sessions WHERE entry_id = ? ORDER BY played_at DESC');
$stmt->bind_param('i', $entry_id);
$stmt->execute();
$sessions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$total_minutes = array_sum(array_column($sessions, 'duration_minutes'));
$total_hours = round($total_minutes / 60, 1);

$pageTitle = $entry['title'] . ' Tracking';
?>

<?php require_once '../includes/header.php' ?>

<main class="flex-1 p-6 lg:p-12">
    <a href="entries.php" class="custom-btn bg-white text-sm mb-6"><i class="fa-solid fa-arrow-left mr-2"></i> Back to Entries</a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

        <!-- Entry info -->
        <div class="custom-card p-0 flex flex-col bg-white h-max overflow-hidden">
            <div class="h-64 border-b-4 border-black relative">
                <img src="<?= htmlspecialchars($entry['image_url']) ?>" alt="<?= htmlspecialchars($entry['title']) ?> cover" class="w-full h-full object-cover">
                <p class="absolute top-4 left-4 bg-custom-yellow border-4 border-black px-3 py-1 font-mono text-sm font-bold uppercase z-10">
                    <?= htmlspecialchars($entry['genre']) ?> | <?= htmlspecialchars($entry['release_year']) ?>
                </p>
            </div>

            <div class="p-6">
                <h1 class="text-center font-grotesk font-bold uppercase text-3xl border-b-4 border-black pb-4">
                    <?= htmlspecialchars($entry['title']) ?>
                </h1>

                <div class="space-y-4 w-full font-mono uppercase mt-8 border-b-4 border-black pb-8 text-sm">
                    <div class="flex justify-between border-b-2 border-gray-400 border-dashed pb-2"><span class="font-bold">Status</span><span class="px-2 text-white bg-black font-bold"><?= htmlspecialchars($entry['status']) ?></span></div>
                    <div class="flex justify-between border-b-2 border-gray-400 border-dashed pb-2"><span class="font-bold">My Rating</span><?= $entry['rating'] ? str_repeat('⭐', $entry['rating']) : 'Not rated' ?></div>
                    <div class="flex justify-between border-b-2 border-gray-400 border-dashed pb-2"><span class="font-bold">Time Tracked</span><span class="px-2 text-black bg-custom-teal font-bold border-2 border-black"><?= $total_hours ?> hr</span></div>
                    <div class="flex justify-between border-b-2 border-gray-400 border-dashed pb-2 font-bold"><span>Started</span><?= $entry['started_at'] ?? 'N/A' ?></div>
                    <div class="flex justify-between font-bold"><span>Finished</span><?= $entry['finished_at'] ?? 'N/A' ?></div>
                </div>

                <div class="space-y-3 mt-6">
                    <button id="openUpdateModalBtn" class="custom-btn w-full bg-custom-yellow">
                        Edit entry
                    </button>
                    <button id="openRemoveModalBtn" class="custom-btn w-full bg-custom-red">
                        Remove entry
                    </button>
                </div>
            </div>
        </div>

        <div class="md:col-span-2 space-y-10">
            <div class="custom-card">
                <h2 class="font-grotesk uppercase text-2xl font-bold mb-3 border-b-4 border-black pb-2">Notes</h2>
                <p class="font-mono text-sm custom-card !bg-custom-bg">
                    <?= $entry['notes'] ? nl2br(htmlspecialchars($entry['notes'])) : 'No notes written yet.' ?>
                </p>
            </div>

            <div class="custom-card">
                <div class="flex justify-between items-end border-b mb-6 border-b-4 border-black pb-4">
                    <h2 class="font-grotesk uppercase text-2xl font-bold">Sessions</h2>
                    <button id="openSessionModalBtn" class="custom-btn !bg-custom-teal">+ Log Time</button>
                </div>

                <?php if (empty($sessions)): ?>
                    <p class="font-mono text-sm font-bold custom-card !bg-custom-bg uppercase text-center">No sessions recorded yet.</p>
                <?php else: ?>
                    <div class="overflow-x-auto custom-shadow custom-border bg-white">
                        <table class="w-full text-left font-mono font-bold text-sm uppercase">
                            <thead class="bg-black text-white">
                                <tr>
                                    <th class="p-4 border-r-4 border-white">Date</th>
                                    <th class="p-4 border-r-4 border-white">Length</th>
                                    <th class="p-4 border-r-4 border-white w-1/2">Notes</th>
                                    <th class="p-4 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sessions as $session): ?>
                                    <tr class="border-b-4 border-black hover:bg-custom-bg transition-colors">
                                        <td class="p-4 border-r-4 border-black"><?= htmlspecialchars($session['played_at']) ?></td>
                                        <td class="p-4 text-center bg-custom-teal border-r-4 border-black"><?= htmlspecialchars($session['duration_minutes']) ?> min</td>
                                        <td class="p-4 border-r-4 border-black normal-case font-normal break-words">
                                            <?= htmlspecialchars($session['notes'] ?? '-') ?>
                                        </td>
                                        <td class="p-3 text-center align-middle">
                                            <form action="../actions/delete_session.php" method="POST" onsubmit="return confirm('Delete this session?');">
                                                <input type="hidden" name="session_id" value="<?= $session['id'] ?>">
                                                <input type="hidden" name="entry_id" value="<?= $session['entry_id'] ?>">
                                                <button type="submit" class="custom-btn bg-custom-red"><i class="fa-solid fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</main>

<!-- Edit entry info -->
<div id="updateModal" class="hidden fixed inset-0 bg-black/80 flex justify-center items-center transition-opacity z-50 p-4">
    <div class="custom-card max-w-lg w-full bg-white !p-8">
        <h2 class="font-grotesk text-3xl font-black uppercase border-b-4 border-black pb-2 mb-6">Edit entry</h2>

        <form action="../actions/edit_entry.php" method="POST" class="flex flex-col gap-4">
            <input type="hidden" name="entry_id" value="<?= $entry_id ?>">

            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-1">
                    <label for="genre" class="uppercase font-mono text-sm font-bold">Status</label>
                    <select name="status" id="status" class="custom-input">
                        <option value="waitlist" <?= $entry['status'] === 'waitlist' ? 'selected' : '' ?>>Waitlist</option>
                        <option value="playing" <?= $entry['status'] === 'playing' ? 'selected' : '' ?>>Playing</option>
                        <option value="finished" <?= $entry['status'] === 'finished' ? 'selected' : '' ?>>Finished</option>
                        <option value="quit" <?= $entry['status'] === 'quit' ? 'selected' : '' ?>>Quit</option>
                    </select>
                </div>

                <div class="flex flex-col gap-1">
                    <label for="rating" class="uppercase font-mono text-sm font-bold">Score Rating (1-5)</label>
                    <select name="rating" id="rating" class="custom-input">
                        <option value="">No Rating</option>
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <option value="<?= $i ?>" <?= $i === $entry['rating'] ? 'selected' : '' ?>><?= str_repeat('⭐', $i) ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 ">
                <div class="flex flex-col gap-1">
                    <label for="started_at" class="uppercase font-mono text-sm font-bold">Date Started</label>
                    <input type="date" name="started_at" id="started_at" placeholder="e.g. RPG, Action" class="custom-input" value="<?= htmlspecialchars($entry['started_at'] ?? '') ?>">
                </div>

                <div class="flex flex-col gap-1">
                    <label for="finished_at" class="uppercase font-mono text-sm font-bold">Date Finished</label>
                    <input type="date" name="finished_at" id="finished_at" class="custom-input" value="<?= htmlspecialchars($entry['finished_at'] ?? '') ?>">
                </div>
            </div>

            <div class="flex flex-col gap-1 mb-4">
                <label for="notes" class="uppercase font-mono text-sm font-bold">Notes</label>
                <textarea name="notes" id="notes" rows="4" class="custom-input"><?= htmlspecialchars($entry['notes'] ?? '') ?></textarea>
            </div>

            <div class="flex justify-end gap-4 border-t-4 border-black pt-6">
                <button type="button" id="closeUpdateModalBtn" class="custom-btn">Cancel</button>
                <button type="submit" class="custom-btn text-lg bg-custom-yellow">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal to confirm entry removal -->
<div id="removeModal" class="hidden fixed inset-0 bg-black/80 flex justify-center items-center transition-opacity z-50 p-4">
    <div class="custom-card max-w-md w-full bg-white">
        <h2 class="font-grotesk text-3xl font-black uppercase border-b-4 border-black pb-2 mb-4 text-center text-custom-red">Confirm</h2>

        <p class="font-mono font-bold uppercase mb-4 text-lg text-center px-4">Remove <span class="text-white bg-black px-2 py-1d"><?= htmlspecialchars($entry['title']) ?></span> from your entries?</p>

        <form action="../actions/delete_entry.php" method="POST" class="mt-8 mb-4">
            <input type="hidden" name="entry_id" value="<?= htmlspecialchars($entry['id']) ?>">

            <div class="flex justify-end gap-4 justify-center">
                <button type="button" id="closeRemoveModalBtn" class="custom-btn bg-white">Cancel</button>
                <button type="submit" class="custom-btn !bg-custom-red">Remove</button>
            </div>
        </form>
    </div>
</div>

<!-- Add session -->
<div id="sessionModal" class="hidden fixed inset-0 bg-black/80 flex justify-center items-center transition-opacity z-50 p-4">
    <div class="custom-card max-w-lg w-full bg-white !p-8">
        <h2 class="font-grotesk text-3xl font-black uppercase border-b-4 border-black pb-2 mb-6">Log Session</h2>

        <form action="../actions/add_session.php" method="POST" class="flex flex-col gap-4">
            <input type="hidden" name="entry_id" value="<?= $entry_id ?>">

            <div class="grid grid-cols-2 gap-3 mb-3">
                <div class="flex flex-col gap-1">
                    <label for="played_at" class="uppercase font-mono text-sm font-bold">Date</label>
                    <input type="date" name="played_at" id="played_at" required value="<?= date('Y-m-d') ?>" class="custom-input">
                </div>

                <div class="flex flex-col gap-1">
                    <label for="duration_minutes" class="uppercase font-mono text-sm font-bold">Minutes played</label>
                    <input type="number" name="duration_minutes" id="duration_minutes" placeholder="90" required class="custom-input">
                </div>
            </div>

            <div class="flex flex-col gap-1 mb-4">
                <label for="notes" class="uppercase font-mono text-sm font-bold">Notes (optional)</label>
                <textarea name="notes" id="notes" rows="3" placeholder="What did you do?" class="custom-input"></textarea>
            </div>

            <div class="flex justify-end gap-4 border-t-4 border-black pt-6">
                <button type="button" id="closeSessionModalBtn" class="custom-btn">Cancel</button>
                <button type="submit" class="custom-btn !bg-custom-teal text-lg">Log Session</button>
            </div>
        </form>
    </div>
</div>

<script>
    // DOM variables
    const updateModal = document.getElementById('updateModal');
    const openUpdateModalBtn = document.getElementById('openUpdateModalBtn');
    const closeUpdateModalBtn = document.getElementById('closeUpdateModalBtn');

    const removeModal = document.getElementById('removeModal');
    const openRemoveModalBtn = document.getElementById('openRemoveModalBtn');
    const closeRemoveModalBtn = document.getElementById('closeRemoveModalBtn');

    const sessionModal = document.getElementById('sessionModal');
    const openSessionModalBtn = document.getElementById('openSessionModalBtn');
    const closeSessionModalBtn = document.getElementById('closeSessionModalBtn');

    // Modal visibility toggles
    const toggleUpdateModal = () => updateModal.classList.toggle('hidden');
    const toggleRemoveModal = () => removeModal.classList.toggle('hidden');
    const toggleSessionModal = () => sessionModal.classList.toggle('hidden');

    // Event listeners for buttons
    if (openUpdateModalBtn) openUpdateModalBtn.addEventListener('click', toggleUpdateModal);
    if (closeUpdateModalBtn) closeUpdateModalBtn.addEventListener('click', toggleUpdateModal);

    if (openRemoveModalBtn) openRemoveModalBtn.addEventListener('click', toggleRemoveModal);
    if (closeRemoveModalBtn) closeRemoveModalBtn.addEventListener('click', toggleRemoveModal);

    if (openSessionModalBtn) openSessionModalBtn.addEventListener('click', toggleSessionModal);
    if (closeSessionModalBtn) closeSessionModalBtn.addEventListener('click', toggleSessionModal);

    // Event listener for clicking outside the modal
    window.addEventListener('click', (e) => {
        if (e.target === updateModal) {
            toggleUpdateModal();
        }
        if (e.target === removeModal) {
            toggleRemoveModal();
        }
        if (e.target === sessionModal) {
            toggleSessionModal();
        }
    });
</script>

<?php require_once '../includes/footer.php' ?>