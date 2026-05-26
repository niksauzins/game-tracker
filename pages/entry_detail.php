<?php
session_start();
require_once __DIR__ . '/../config/db.php';
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
    redirect('entries.php', 'error', __('flash_entry_not_found'));
}

// Get all sessions for this entry
$stmt = $conn->prepare('SELECT * FROM sessions WHERE entry_id = ? ORDER BY played_at DESC');
$stmt->bind_param('i', $entry_id);
$stmt->execute();
$sessions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$total_minutes = array_sum(array_column($sessions, 'duration_minutes'));
$total_hours = round($total_minutes / 60, 1);

$pageTitle = $entry['title'] . ' ' . __('index_title_2') . ' | ' . __('app_title');

$old = $_SESSION['old'] ?? [];
$openModal = $_SESSION['open_modal'] ?? '';
unset($_SESSION['old'], $_SESSION['open_modal']);
?>

<?php require_once BASE_PATH . '/includes/header.php' ?>

<main class="flex-1 p-6 lg:p-12">
    <a href="<?= BASE_URL ?>/pages/entries.php" class="custom-btn bg-white text-sm mb-6"><i class="fa-solid fa-arrow-left mr-2"></i> <?= __('back_to_entries') ?></a>

    <?php if (!$openModal) renderFlash() ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-10">

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
                    <div class="flex justify-between border-b-2 border-gray-400 border-dashed pb-2"><span class="font-bold"><?= __('status') ?></span><span class="px-2 text-white bg-black font-bold"><?= htmlspecialchars(translateStatus($entry['status'])) ?></span></div>
                    <div class="flex justify-between border-b-2 border-gray-400 border-dashed pb-2"><span class="font-bold"><?= __('my_rating') ?></span><?= $entry['rating'] ? str_repeat('⭐', $entry['rating']) : __('not_rated') ?></div>
                    <div class="flex justify-between border-b-2 border-gray-400 border-dashed pb-2"><span class="font-bold"><?= __('time_tracked') ?></span><span class="px-2 text-black bg-custom-teal font-bold border-2 border-black"><?= $total_hours ?> <?= __('hours_short') ?></span></div>
                    <div class="flex justify-between border-b-2 border-gray-400 border-dashed pb-2 font-bold"><span><?= __('started') ?></span><?= $entry['started_at'] ?? 'N/A' ?></div>
                    <div class="flex justify-between font-bold"><span><?= __('finished') ?></span><?= $entry['finished_at'] ?? 'N/A' ?></div>
                </div>

                <div class="space-y-3 mt-6">
                    <button id="openUpdateModalBtn" class="custom-btn w-full bg-custom-yellow">
                        <?= __('btn_edit_entry') ?>
                    </button>
                    <button id="openRemoveModalBtn" class="custom-btn w-full bg-custom-red">
                        <?= __('btn_remove_entry') ?>
                    </button>
                </div>
            </div>
        </div>

        <div class="md:col-span-2 space-y-6 md:space-y-10">
            <div class="custom-card">
                <h2 class="font-grotesk uppercase text-2xl font-bold mb-3 border-b-4 border-black pb-2"><?= __('notes') ?></h2>
                <p class="font-mono text-sm custom-card !bg-custom-bg">
                    <?= $entry['notes'] ? nl2br(htmlspecialchars($entry['notes'])) : __('no_notes') ?>
                </p>
            </div>

            <div class="custom-card">
                <div class="flex justify-between items-end border-b mb-6 border-b-4 border-black pb-4">
                    <h2 class="font-grotesk uppercase text-2xl font-bold"><?= __('sessions') ?></h2>
                    <button id="openSessionModalBtn" class="custom-btn !bg-custom-teal"><?= __('btn_log_session') ?></button>
                </div>

                <?php if (empty($sessions)): ?>
                    <p class="font-mono text-sm font-bold custom-card !bg-custom-bg uppercase text-center"><?= __('no_sessions') ?></p>
                <?php else: ?>
                    <div class="hidden md:block overflow-x-auto custom-shadow custom-border bg-white">
                        <table class="w-full text-left font-mono font-bold text-sm uppercase">
                            <thead class="bg-black text-white">
                                <tr>
                                    <th class="p-2 md:p-4 text-xs sm:text-sm border-r-2 sm:border-r-4 border-white"><?= __('date') ?></th>
                                    <th class="p-2 md:p-4 text-xs sm:text-sm border-r-2 sm:border-r-4 border-white"><?= __('table_length') ?></th>
                                    <th class="p-2 md:p-4 text-xs sm:text-sm border-r-2 sm:border-r-4 border-white w-1/2"><?= __('notes') ?></th>
                                    <th class="p-2 md:p-4 text-xs sm:text-sm sm:text-center"><?= __('table_action') ?></th>
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
                                            <form action="<?= BASE_URL ?>/actions/delete_session.php" method="POST" onsubmit="return confirm('<?= __('confirm_delete_session') ?>');">
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

                    <!-- Mobile session cards -->
                    <div class="block md:hidden space-y-4">
                        <?php foreach ($sessions as $session): ?>
                            <div class="custom-card bg-white space-y-3">
                                <div class="flex justify-between items-center border-b-2 border-black border-dashed pb-2">
                                    <span class="font-mono font-bold text-sm text-gray-700"><?= htmlspecialchars($session['played_at']) ?></span>
                                    <span class="bg-custom-teal border-2 border-black px-2 py-0.5 text-xs font-bold font-mono uppercase"><?= htmlspecialchars($session['duration_minutes']) ?> min</span>
                                </div>
                                <p class="font-mono text-xs text-black normal-case leading-relaxed break-words">
                                    <span class="font-bold uppercase block text-[10px] text-gray-500 mb-1"><?= __('session');
                                                                                                            __('notes') ?>:</span>
                                    <?= htmlspecialchars($session['notes'] ?? __('no_notes')) ?>
                                </p>
                                <div class="flex justify-end pt-2">
                                    <form action="<?= BASE_URL ?>/actions/delete_session.php" method="POST" onsubmit="return confirm('Delete this session?');">
                                        <input type="hidden" name="session_id" value="<?= $session['id'] ?>">
                                        <input type="hidden" name="entry_id" value="<?= $session['entry_id'] ?>">
                                        <button type="submit" class="custom-btn bg-custom-red !py-1 w-full text-xs"><i class="fa-solid fa-trash mr-1"></i><?= __('remove_session') ?></button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</main>

<!-- Edit entry info -->
<div id="updateModal" class="<?= $openModal === 'edit_entry' ? '' : 'hidden' ?> fixed inset-0 bg-black/80 flex justify-center items-center transition-opacity z-50 p-4">
    <div class="custom-card max-w-lg w-full bg-white !p-8">
        <h2 class="font-grotesk text-3xl font-black uppercase border-b-4 border-black pb-2 mb-6"><?= __('modal_edit_entry_title') ?></h2>

        <form action="<?= BASE_URL ?>/actions/edit_entry.php" method="POST" class="flex flex-col gap-4">
            <input type="hidden" name="entry_id" value="<?= $entry_id ?>">

            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-1">
                    <label for="genre" class="uppercase font-mono text-sm font-bold"><?= __('status') ?></label>
                    <select name="status" id="status" class="custom-input">
                        <option value="waitlist" <?= ($old['status'] ?? $entry['status']) === 'waitlist' ? 'selected' : '' ?>><?= __('status_waitlist') ?></option>
                        <option value="playing" <?= ($old['status'] ?? $entry['status']) === 'playing' ? 'selected' : '' ?>><?= __('status_playing') ?></option>
                        <option value="finished" <?= ($old['status'] ?? $entry['status']) === 'finished' ? 'selected' : '' ?>><?= __('status_finished') ?></option>
                        <option value="quit" <?= ($old['status'] ?? $entry['status']) === 'quit' ? 'selected' : '' ?>><?= __('status_quit') ?></option>
                    </select>
                </div>

                <div class="flex flex-col gap-1">
                    <label for="rating" class="uppercase font-mono text-sm font-bold"><?= __('field_rating') ?></label>
                    <select name="rating" id="rating" class="custom-input">
                        <option value=""><?= __('field_rating_none') ?></option>
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <option value="<?= $i ?>" <?= $i === (int)($old['rating'] ?? $entry['rating']) ? 'selected' : '' ?>><?= str_repeat('⭐', $i) ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <div class="flex flex-col md:grid grid-cols-2 gap-4 ">
                <div class="flex flex-col gap-1">
                    <label for="started_at" class="uppercase font-mono text-sm font-bold"><?= __('field_date_started') ?></label>
                    <input type="date" name="started_at" id="started_at" class="custom-input w-full" value="<?= htmlspecialchars($old['started_at'] ?? $entry['started_at'] ?? '') ?>">
                </div>

                <div class="flex flex-col gap-1">
                    <label for="finished_at" class="uppercase font-mono text-sm font-bold"><?= __('field_date_finished') ?></label>
                    <input type="date" name="finished_at" id="finished_at" class="custom-input w-full" value="<?= htmlspecialchars($old['finished_at'] ?? $entry['finished_at'] ?? '') ?>">
                </div>
            </div>

            <div class="flex flex-col gap-1 mb-4">
                <label for="notes" class="uppercase font-mono text-sm font-bold"><?= __('notes') ?></label>
                <textarea name="notes" id="notes" rows="4" class="custom-input"><?= htmlspecialchars($old['notes'] ?? $entry['notes'] ?? '') ?></textarea>
            </div>

            <?php if ($openModal === 'edit_entry') renderFlash('-mt-2') ?>

            <div class="flex justify-end gap-4 border-t-4 border-black pt-6">
                <button type="button" id="closeUpdateModalBtn" class="custom-btn"><?= __('cancel') ?></button>
                <button type="submit" class="custom-btn text-lg bg-custom-yellow"><?= __('save_changes') ?></button>
            </div>
        </form>
    </div>
</div>

<!-- Modal to confirm entry removal -->
<div id="removeModal" class="hidden fixed inset-0 bg-black/80 flex justify-center items-center transition-opacity z-50 p-4">
    <div class="custom-card max-w-md w-full bg-white">
        <h2 class="font-grotesk text-3xl font-black uppercase border-b-4 border-black pb-2 mb-4 text-center text-custom-red"><?= __('modal_delete_title') ?></h2>

        <p class="font-mono font-bold uppercase mb-4 text-lg text-center px-4"><?= __('modal_remove_title') ?> <span class="text-white bg-black px-2 py-1d"><?= htmlspecialchars($entry['title']) ?></span> <?= __('modal_remove_prompt') ?></p>

        <form action="<?= BASE_URL ?>/actions/delete_entry.php" method="POST" class="mt-8 mb-4">
            <input type="hidden" name="entry_id" value="<?= htmlspecialchars($entry['id']) ?>">

            <div class="flex justify-end gap-4 justify-center">
                <button type="button" id="closeRemoveModalBtn" class="custom-btn bg-white"><?= __('cancel') ?></button>
                <button type="submit" class="custom-btn !bg-custom-red"><?= __('btn_remove') ?></button>
            </div>
        </form>
    </div>
</div>

<!-- Add session -->
<div id="sessionModal" class="<?= $openModal === 'add_session' ? '' : 'hidden' ?> fixed inset-0 bg-black/80 flex justify-center items-center transition-opacity z-50 p-4">
    <div class="custom-card max-w-lg w-full bg-white !p-8">
        <h2 class="font-grotesk text-3xl font-black uppercase border-b-4 border-black pb-2 mb-6"><?= __('modal_log_session_title') ?></h2>

        <form action="<?= BASE_URL ?>/actions/add_session.php" method="POST" class="flex flex-col gap-4">
            <input type="hidden" name="entry_id" value="<?= $entry_id ?>">

            <div class="flex flex-col md:grid grid-cols-2 gap-3 mb-3">
                <div class="flex flex-col gap-1">
                    <label for="played_at" class="uppercase font-mono text-sm font-bold"><?= __('date') ?></label>
                    <input type="date" name="played_at" id="played_at" required value="<?= $old['played_at'] ?? date('Y-m-d') ?>" class="custom-input w-full">
                </div>

                <div class="flex flex-col gap-1">
                    <label for="duration_minutes" class="uppercase font-mono text-sm font-bold"><?= __('field_duration') ?></label>
                    <input type="number" name="duration_minutes" id="duration_minutes" placeholder="90" required value="<?= htmlspecialchars($old['duration_minutes'] ?? '') ?>" class="custom-input">
                </div>
            </div>

            <div class="flex flex-col gap-1 mb-4">
                <label for="notes" class="uppercase font-mono text-sm font-bold"><?= __('field_notes_optional') ?></label>
                <textarea name="notes" id="notes" rows="3" placeholder="<?= __('field_notes_placeholder') ?>" class="custom-input"><?= htmlspecialchars($old['notes'] ?? '') ?></textarea>
            </div>

            <?php if ($openModal === 'add_session') renderFlash('-mt-2') ?>

            <div class="flex justify-end gap-4 border-t-4 border-black pt-6">
                <button type="button" id="closeSessionModalBtn" class="custom-btn"><?= __('cancel') ?></button>
                <button type="submit" class="custom-btn !bg-custom-teal text-lg"><?= __('btn_log_session_submit') ?></button>
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

<?php require_once BASE_PATH . '/includes/footer.php' ?>