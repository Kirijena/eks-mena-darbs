<?php
session_start();
$current_page = 'latest';

?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senās Teikas - Jaunākie Ieraksti</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="content">
        <section class="latest-entries">
            <h2>Jaunākie Ieraksti</h2>
            <div class="entries-grid">
                <?php if(mysqli_num_rows($result) > 0): ?>
                    <?php while($entry = mysqli_fetch_assoc($result)): ?>
                        <article class="entry-card">
                            <div class="entry-image">
                                <?php if(!empty($entry['image_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($entry['image_url']); ?>" alt="<?php echo htmlspecialchars($entry['title']); ?>">
                                <?php else: ?>
                                    <img src="images/default-entry.jpg" alt="Default image">
                                <?php endif; ?>
                            </div>
                            <div class="entry-content">
                                <h3><?php echo htmlspecialchars($entry['title']); ?></h3>
                                <p class="entry-excerpt"><?php echo htmlspecialchars(substr($entry['content'], 0, 150)) . '...'; ?></p>
                                <div class="entry-meta">
                                    <span class="entry-date">
                                        <i class="far fa-calendar"></i>
                                        <?php echo date('d.m.Y', strtotime($entry['created_at'])); ?>
                                    </span>
                                    <span class="entry-category">
                                        <i class="fas fa-folder"></i>
                                        <?php echo htmlspecialchars($entry['category']); ?>
                                    </span>
                                </div>
                                <a href="entry.php?id=<?php echo (int)$entry['id']; ?>" class="read-more">Lasīt vairāk</a>
                            </div>
                        </article>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-entries">
                        <p>Nav pieejamu ierakstu.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <div id="authModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <?php include 'includes/login-form.php'; ?>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
<?php
// Close database connection
mysqli_close($conn);
?>
