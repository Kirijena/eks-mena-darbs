<?php
session_start();
require '../includes/connect_db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_POST['delete_record'])) {
    $record_id = (int)$_POST['record_id'];
    $stmt = $savienojums->prepare("DELETE FROM eksamens_ieraksti WHERE ieraksti_id = ?");
    $stmt->bind_param("i", $record_id);
    $stmt->execute();
    $stmt->close();
}

if (isset($_POST['create_record'])) {
    $veids = trim($_POST['veids']);
    $zem_tipa = trim($_POST['zem_tipa']);
    $virsraksti = trim($_POST['virsraksti']);
    $iss_apraksts = trim($_POST['iss_apraksts']);

    $stmt = $savienojums->prepare("INSERT INTO eksamens_ieraksti (veids, zem_tipa, virsraksti, iss_apraksts) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $veids, $zem_tipa, $virsraksti, $iss_apraksts);
    $stmt->execute();
    $stmt->close();
}

if (isset($_POST['update_record'])) {
    $record_id = (int)$_POST['record_id'];
    $veids = trim($_POST['veids']);
    $zem_tipa = trim($_POST['zem_tipa']);
    $virsraksti = trim($_POST['virsraksti']);
    $iss_apraksts = trim($_POST['iss_apraksts']);

    $stmt = $savienojums->prepare("UPDATE eksamens_ieraksti SET veids = ?, zem_tipa = ?, virsraksti = ?, iss_apraksts = ? WHERE ieraksti_id = ?");
    $stmt->bind_param("ssssi", $veids, $zem_tipa, $virsraksti, $iss_apraksts, $record_id);
    $stmt->execute();
    $stmt->close();
}



$result = $savienojums->query("SELECT * FROM eksamens_ieraksti ORDER BY ieraksti_id DESC");
?>


<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mītoloģijas Ierakstu Pārvaldība</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="fas fa-book"></i> Mītoloģijas Ierakstu Pārvaldība</h1>
            <div class="admin-actions">
            <a href="users.php" class="admin-btn">
            Lietotāji
        </a>
                <button onclick="showCreateRecordForm()" class="admin-btn">
                    <i class="fas fa-plus"></i> Jauns Ieraksts
                </button>
                <a href="logout.php" class="admin-btn logout">
                    <i class="fas fa-sign-out-alt"></i> Iziet
                </a>
            </div>
        </div>

        <div class="users-table-container">
            <table class="users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Veids</th>
                        <th>Zem Tipa</th>
                        <th>Virsraksti</th>
                        <th>Īss Apraksts</th>
                        <th>Darbības</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($record = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($record['ieraksti_id']) ?></td>
                        <td><?= htmlspecialchars($record['veids']) ?></td>
                        <td><?= htmlspecialchars($record['zem_tipa']) ?></td>
                        <td><?= htmlspecialchars($record['virsraksti']) ?></td>
                        <td><?= htmlspecialchars($record['iss_apraksts']) ?></td>
                        <td class="action-buttons">
                            <button onclick="showEditRecordForm(<?= htmlspecialchars(json_encode($record)) ?>)" class="edit-btn">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" class="inline-form" onsubmit="return confirm('Vai tiešām vēlaties dzēst šo ierakstu?')">
                                <input type="hidden" name="record_id" value="<?= $record['ieraksti_id'] ?>">
                                <button type="submit" name="delete_record" class="delete-btn">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Record Modal -->
    <div id="createRecordModal" class="modal">
        <div class="modal-content">
            <h2><i class="fas fa-plus"></i> Jauns Ieraksts</h2>
            <form method="POST">
            <div class="form-group">
                <label for="veids">Veids</label>
                    <select name="veids" required>
                        <option value="Skandinavu Mitologija">Skandinavu Mitoloģija</option>
                        <option value="Sengrieku Mitologija">Sengrieku Mitoloģija</option>
                        <option value="Romiesu Mitologija">Romiešu Mitoloģija</option>
                        <option value="Egiptes Mitologija">Egiptes Mitoloģija</option>
                        <option value="Slavu Mitologija">Slāvu Mitoloģija</option>
                        <option value="Keltu Mitologija">Ķeltu Mitoloģija</option>
                        <option value="Azijas Mitologija">Āzijas Mitoloģija</option>
                    </select>
            </div>

            <div class="form-group">
                <label for="zem_tipa">Zem Tipa</label>
                    <select name="zem_tipa" required>
                        <option value="Dievi">Dievi</option>
                        <option value="Mitiskas Butnes">Mitiskas Būtnes</option>
                        <option value="Svarigi Notikumi">Svarīgi Notikumi</option>
                        <option value="Olimpa Dievi">Olimpa Dievi</option>
                        <option value="Varoni un Legendas">Varoņi un Leģendas</option>
                        <option value="Varoni un Legendas">Mitiskas Butnes</option>
                        <option value="Varoni un Legendas">Svarigi Notikumi</option>
                        <option value="Varoni un Legendas">Varoņi un Leģendas</option>
                        <option value="Varoni un Legendas">Galvenie Dievi</option>
                        <option value="Varoni un Legendas">Romas Legendas</option>
                        <option value="Varoni un Legendas">Svetie Simboli un Rituali</option>
                        <option value="Varoni un Legendas">Pecnaves Dzive</option>
                        <option value="Varoni un Legendas">Dabas Gari</option>
                        <option value="Varoni un Legendas">Dievi un Dievibas</option>
                        <option value="Varoni un Legendas">Svetas Vietas un Tradicijas</option>
                        <option value="Varoni un Legendas">Svetas Vietas un Simboli</option>
                        <option value="Varoni un Legendas">Kiniesu Mitologija</option>
                        <option value="Varoni un Legendas">Japaņu Mitologija</option>
                        <option value="Varoni un Legendas">Citas Azijas Tradicijas</option>
                        <option value="Varoni un Legendas">Cīnas Makslas un Karotaji</option>
                    </select>
            </div>

                <div class="form-group">
                    <input type="text" name="virsraksti" placeholder="Virsraksti" required>
                </div>
                <div class="form-group">
                    <input type="text" name="iss_apraksts" placeholder="Īss Apraksts" required>
                </div>
                <button type="submit" name="create_record" class="submit-btn">Saglabāt</button>
                <button type="button" onclick="hideCreateRecordModal()" class="cancel-btn">
                    <i class="fas fa-times"></i> Atcelt
                </button>

            </form>
        </div>
    </div>

    <script>
    function showCreateRecordForm() {
        document.getElementById('createRecordModal').style.display = 'block';
    }

    function showEditRecordForm(record) {
        console.log(record);
    }

    function showCreateRecordForm() {
    document.getElementById('createRecordModal').style.display = 'block';
}

function hideCreateRecordModal() {
    document.getElementById('createRecordModal').style.display = 'none';
}

function showEditRecordForm(record) {
    console.log(record);
}

function showEditRecordForm(record) {
   
    const modal = document.getElementById('createRecordModal');

    modal.querySelector('input[name="veids"]').value = record.veids;
    modal.querySelector('input[name="zem_tipa"]').value = record.zem_tipa;
    modal.querySelector('input[name="virsraksti"]').value = record.virsraksti;
    modal.querySelector('input[name="iss_apraksts"]').value = record.iss_apraksts;

    
    modal.querySelector('h2').innerHTML = '<i class="fas fa-edit"></i> Rediģēt Ierakstu';

   
    let recordIdInput = document.createElement('input');
    recordIdInput.type = 'hidden';
    recordIdInput.name = 'record_id';
    recordIdInput.value = record.ieraksti_id;
    modal.querySelector('form').appendChild(recordIdInput);

    
    modal.style.display = 'block';
}


    
    </script>
</body>
</html>
