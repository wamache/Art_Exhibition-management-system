<?php
require_once __DIR__ . '/../config/db.php';

// Handle form submission to save content and enable/disable sections
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Enable/disable sections
    foreach ($_POST['enabled'] ?? [] as $sectionId => $value) {
        $stmt = $conn->prepare("UPDATE sections SET enabled = ? WHERE id = ?");
        $enabled = $value === '1' ? 1 : 0;
        $stmt->bind_param("ii", $enabled, $sectionId);
        $stmt->execute();
    }

    // Update section contents
    foreach ($_POST['content'] ?? [] as $sectionId => $contents) {
        foreach ($contents as $key => $value) {
            // Check if content exists for this key
            $checkStmt = $conn->prepare("SELECT id FROM section_contents WHERE section_id = ? AND `key` = ?");
            $checkStmt->bind_param("is", $sectionId, $key);
            $checkStmt->execute();
            $result = $checkStmt->get_result();

            if ($result->num_rows > 0) {
                // Update existing content
                $updateStmt = $conn->prepare("UPDATE section_contents SET `value` = ? WHERE section_id = ? AND `key` = ?");
                $updateStmt->bind_param("sis", $value, $sectionId, $key);
                $updateStmt->execute();
            } else {
                // Insert new content
                $insertStmt = $conn->prepare("INSERT INTO section_contents (section_id, `key`, `value`) VALUES (?, ?, ?)");
                $insertStmt->bind_param("iss", $sectionId, $key, $value);
                $insertStmt->execute();
            }
        }
    }

    echo '<div class="alert alert-success">Settings saved!</div>';
}

// Fetch sections and their content
$sections = $conn->query("SELECT * FROM sections ORDER BY position");
$sectionsData = [];
while ($section = $sections->fetch_assoc()) {
    $sectionId = $section['id'];
    $contentsRes = $conn->query("SELECT `key`, `value` FROM section_contents WHERE section_id = $sectionId");
    $contents = [];
    while ($row = $contentsRes->fetch_assoc()) {
        $contents[$row['key']] = $row['value'];
    }
    $section['contents'] = $contents;
    $sectionsData[] = $section;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Panel - Manage Sections</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
  <div class="container py-4">
    <h1>Admin Panel: Manage Sections</h1>
    <form method="post">
      <?php foreach ($sectionsData as $section): ?>
        <div class="card mb-4">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><?= htmlspecialchars($section['name']) ?></h4>
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" name="enabled[<?= $section['id'] ?>]" value="1" id="enabled_<?= $section['id'] ?>"
                <?= $section['enabled'] ? 'checked' : '' ?>>
              <label class="form-check-label" for="enabled_<?= $section['id'] ?>">Enabled</label>
            </div>
          </div>
          <div class="card-body">
            <?php if ($section['slug'] === 'hero'): ?>
              <div class="mb-3">
                <label class="form-label">Headline</label>
                <input type="text" class="form-control" name="content[<?= $section['id'] ?>][headline]" value="<?= htmlspecialchars($section['contents']['headline'] ?? '') ?>">
              </div>
              <div class="mb-3">
                <label class="form-label">Subheadline</label>
                <input type="text" class="form-control" name="content[<?= $section['id'] ?>][subheadline]" value="<?= htmlspecialchars($section['contents']['subheadline'] ?? '') ?>">
              </div>
              <div class="mb-3">
                <label class="form-label">Primary CTA Text</label>
                <input type="text" class="form-control" name="content[<?= $section['id'] ?>][cta_primary]" value="<?= htmlspecialchars($section['contents']['cta_primary'] ?? '') ?>">
              </div>
              <div class="mb-3">
                <label class="form-label">Secondary CTA Text</label>
                <input type="text" class="form-control" name="content[<?= $section['id'] ?>][cta_secondary]" value="<?= htmlspecialchars($section['contents']['cta_secondary'] ?? '') ?>">
              </div>
            <?php elseif ($section['slug'] === 'featured'): ?>
              <div class="mb-3">
                <label class="form-label">Section Title</label>
                <input type="text" class="form-control" name="content[<?= $section['id'] ?>][title]" value="<?= htmlspecialchars($section['contents']['title'] ?? '') ?>">
              </div>
              <p>Exhibitions are managed separately and displayed automatically.</p>
            <?php else: ?>
              <p>No editable content for this section yet.</p>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
      <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
  </div>
</body>
</html>
