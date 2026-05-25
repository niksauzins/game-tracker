<?php
$id = htmlspecialchars($cardData['id']);
$title = htmlspecialchars($cardData['title']);
$description = htmlspecialchars($cardData['description']);
$genre = htmlspecialchars($cardData['genre']);
$release_year = htmlspecialchars($cardData['release_year']);
$image_url = htmlspecialchars($cardData['image_url']);

$status = htmlspecialchars(translateStatus($cardData['status'] ?? ''));
$isEntry = $cardData['is_entry'] ?? false;
$entryId = $cardData['entry_id'] ?? null;
$isAlreadyAdded = !empty($entryId);
$link = $isEntry ? "entry_detail.php?id={$entryId}" : "game_detail.php?id={$id}";
?>

<div class="custom-card relative flex flex-col h-full bg-white custom-hover group overflow-hidden">
    <a href="<?= $link ?>" class="absolute inset-0 z-10"></a>

    <div class="custom-border overflow-hidden h-48 mb-4 relative">
        <img src="<?= $image_url ?>" alt="<?php $title ?> cover" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-300">

        <p class="absolute top-2 left-2 bg-custom-yellow font-mono font-bold text-xs uppercase px-3 py-1 custom-border z-20"><?= $genre ?></p>
    </div>

    <div class="flex-1 pb-4 flex flex-col relative z-20">
        <h2 class="font-grotesk font-black text-2xl uppercase leading-none mb-2 line-clamp-2"><?= $title ?></h2>

        <div class="mb-3">
            <p class="font-mono text-xs font-bold bg-black text-white px-2 py-1 inline-block"><?= $release_year ?></p>
        </div>

        <?php if (!$isEntry): ?>
            <p class="font-mono text-sm text-gray-800 leading-relaxed mt-auto pt-3 border-t-2 border-black border-dashed line-clamp-3"><?= $description ?></p>
        <?php else: ?>
            <p class="font-mono text-sm text-gray-800 leading-relaxed mt-auto line-clamp-2"><?= $description ?></p>
        <?php endif; ?>

    </div>

    <!-- When the user already added the game, don't show the button to add again -->
    <?php if (!$isAlreadyAdded): ?>
        <form action="../actions/add_entry.php" method="POST" class="relative z-30 mt-auto pt-2">
            <input type="hidden" name="game_id" value="<?= $id ?>">
            <button type="submit" class="custom-btn !bg-custom-teal w-full text-sm"><?= __('card_add_to_entries') ?></button>
        </form>
    <?php else: ?>
        <p class="border-t-2 border-black border-dashed pt-5 font-mono uppercase font-bold text-sm pb-1"><?= __('status') ?>: <span class="bg-custom-teal px-2 py-1 border-2 border-black"><?= $status ?></span></p>
    <?php endif; ?>
</div>