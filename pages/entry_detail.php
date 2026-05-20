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

// TODO: Get sessions and calculate total time played
$total_hours = 0;

$pageTitle = $entry['title'] . ' Tracking';
?>

<?php require_once '../includes/header.php' ?>

<main class="min-h-screen max-w-5xl w-full mx-auto m-6">
    <div class="grid grid-cols-3 gap-6">

        <!-- Entry info -->
        <div class=" w-full border shadow-sm rounded-xl relative bg-white overflow-hidden">
            <img src="<?= htmlspecialchars($entry['image_url']) ?>" alt="<?= htmlspecialchars($entry['title']) ?> cover image" class="w-full h-64 object-cover rounded-lg rounded-b-none">

            <div class="p-4">
                <h1 class="text-center text-2xl font-bold text-gray-900 mb-4 md:mb-1">
                    <?= htmlspecialchars($entry['title']) ?>
                </h1>
                <p class="text-sm text-center text-gray-500 border-b pb-4">
                    <?= htmlspecialchars($entry['genre']) ?> (<?= htmlspecialchars($entry['release_year']) ?>)
                </p>

                <div class="w-full pt-4 space-y-2 text-sm text-gray-700">
                    <div><span class="font-semibold">Status:</span> <span class="capitalize px-2 py-0.5 bg-blue-100 text-blue-800 rounded-full text-xs font-medium"><?= htmlspecialchars($entry['status']) ?></span></div>
                    <div><span class="font-semibold">My Rating:</span> <?= $entry['rating'] ? str_repeat('⭐', $entry['rating']) : 'Not rated yet' ?></div>
                    <div><span class="font-semibold">Time Tracked:</span> <?= $total_hours ?> hrs</div>
                    <div><span class="font-semibold">Started:</span> <?= $entry['started_at'] ?? 'N/A' ?></div>
                    <div><span class="font-semibold">Finished:</span> <?= $entry['finished_at'] ?? 'N/A' ?></div>
                </div>

                <button id="openUpdateModalBtn" class="w-full mt-6 bg-gray-800 hover:bg-gray-900 text-white font-medium py-2 rounded-lg text-sm transition">
                    Update Status / Note
                </button>
                <button id="openRemoveModalBtn" class="w-full mt-2 bg-red-600 hover:bg-red-700 text-white font-medium py-2 rounded-lg text-sm transition">
                    Remove
                </button>
            </div>
        </div>

        <div class="md:col-span-2 space-y-6">
            <div class="border shadow-sm p-6 rounded-lg">
                <h2 class="text-xl font-bold text-gray-900 mb-3">Notes</h2>
                <p class="text-gray-600 text-sm bg-gray-50 p-3 rounded-lg border italic">
                    <?= $entry['notes'] ? htmlspecialchars($entry['notes']) : 'No personal summary written yet.' ?>
                </p>
            </div>

            <div class="border shadow-sm p-6 rounded-lg">
                <div class="flex justify-between items-center border-b pb-2 mb-4">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Session History</h2>
                    <button id="openSessionModalBtn" class="bg-green-600 rounded-md text-white font-medium text-xs px-3 py-1.5">+ Log Session</button>
                </div>
                <div class="text-sm italic text-gray-600">
                    <p>No sessions tracked yet.</p>
                </div>
            </div>
        </div>

    </div>
</main>

<!-- Edit entry info -->
<div id="updateModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex justify-center items-center transition-opacity duration-300 z-50">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6 mx-4 transform transition-all duration-300">
        <h2 class="font-bold text-2xl text-gray-800 mb-4 w-full border-b pb-3">Update Details</h2>

        <form action="../actions/edit_entry.php" method="POST">
            <input type="hidden" name="entry_id" id="entry_id" value="<?= $entry_id ?>">

            <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                    <label for="genre" class="block font-medium text-sm text-gray-700 mb-1">Status</label>
                    <select name="status" id="status" class="w-full border rounded-lg p-2 text-sm">
                        <option value="waitlist" <?= $entry['status'] === 'waitlist' ? 'selected' : '' ?>>Waitlist</option>
                        <option value="playing" <?= $entry['status'] === 'playing' ? 'selected' : '' ?>>Playing</option>
                        <option value="finished" <?= $entry['status'] === 'finished' ? 'selected' : '' ?>>Finished</option>
                        <option value="quit" <?= $entry['status'] === 'quit' ? 'selected' : '' ?>>Quit</option>
                    </select>
                </div>

                <div>
                    <label for="rating" class="block font-medium text-sm text-gray-700 mb-1">Score Rating (1-5)</label>
                    <select name="rating" id="rating" class="w-full border rounded-lg p-2 text-sm">
                        <option value="">No Rating</option>
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <option value="<?= $i ?>" <?= $i === $entry['rating'] ? 'selected' : '' ?>><?= str_repeat('⭐', $i) ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                    <label for="started_at" class="block font-medium text-sm text-gray-700 mb-1">Date Started</label>
                    <input type="date" name="started_at" id="started_at" placeholder="e.g. RPG, Action" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?= htmlspecialchars($entry['started_at'] ?? '') ?>">
                </div>

                <div>
                    <label for="finished_at" class="block font-medium text-sm text-gray-700 mb-1">Date Finished</label>
                    <input type="date" name="finished_at" id="finished_at" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?= htmlspecialchars($entry['finished_at'] ?? '') ?>">
                </div>
            </div>

            <div class="mb-5">
                <label for="notes" class="block font-medium text-sm text-gray-700 mb-1">Notes</label>
                <textarea name="notes" id="notes" rows="4" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($entry['notes'] ?? '') ?></textarea>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" id="closeUpdateModalBtn" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal to confirm entry removal -->
<div id="removeModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex justify-center items-center transition-opacity duration-300 z-50">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6 mx-4 transform transition-all duration-300">
        <h2 class="font-bold text-2xl text-gray-800 mb-4 w-full border-b pb-3">Remove Confirmation</h2>

        <p class="text-gray-700 mb-4">Are you sure you want to remove <span class="font-bold"><?= htmlspecialchars($entry['title']) ?></span> from your entries?</p>

        <form action="../actions/delete_entry.php" method="POST">
            <input type="hidden" name="entry_id" id="entry_id" value="<?= htmlspecialchars($entry['id']) ?>">

            <div class="flex justify-end gap-2">
                <button type="button" id="closeRemoveModalBtn" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg shadow">Remove</button>
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

    // Modal visibility toggles
    const toggleUpdateModal = () => updateModal.classList.toggle('hidden');
    const toggleRemoveModal = () => removeModal.classList.toggle('hidden');

    // Event listeners for buttons
    if (openUpdateModalBtn) openUpdateModalBtn.addEventListener('click', toggleUpdateModal);
    if (closeUpdateModalBtn) closeUpdateModalBtn.addEventListener('click', toggleUpdateModal);

    if (openRemoveModalBtn) openRemoveModalBtn.addEventListener('click', toggleRemoveModal);
    if (closeRemoveModalBtn) closeRemoveModalBtn.addEventListener('click', toggleRemoveModal);

    // Event listener for clicking outside the modal
    window.addEventListener('click', (e) => {
        if (e.target === updateModal) {
            toggleUpdateModal();
        }
        if (e.target === removeModal) {
            toggleRemoveModal();
        }
    });
</script>

<?php require_once '../includes/footer.php' ?>