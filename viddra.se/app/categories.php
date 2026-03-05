<?php
require_once __DIR__ . '/../includes/bootstrap.php';
Auth::requireLogin();
Auth::requireVerifiedEmail();

$hid = Household::currentId();
Category::seedDefaults($hid);

if ($_SERVER['REQUEST_METHOD']==='POST'){
  if (isset($_POST['create'])){
    Category::create($hid, $_POST['name'], $_POST['type']);
  }
  if (isset($_POST['delete'])){
    Category::delete($hid, $_POST['delete']);
  }
  header("Location: /app/categories.php"); exit;
}

$cats = Category::all($hid);

$page_title = "Categories — Viddra";
include __DIR__ . '/../includes/header.php';
?>

<section class="section">
<div class="container">
<div class="card big">

<h1>Categories</h1>
<p class="muted">These define your structure.</p>

<table style="width:100%;margin-top:14px">
<tr><th align="left">Name</th><th align="left">Type</th><th></th></tr>
<?php foreach ($cats as $c): ?>
<tr>
<td><?php echo htmlspecialchars($c['name']); ?></td>
<td><?php echo htmlspecialchars($c['type']); ?></td>
<td align="right">
<form method="post" style="display:inline">
<button name="delete" value="<?php echo (int)$c['id']; ?>" class="btn">Delete</button>
</form>
</td>
</tr>
<?php endforeach; ?>
</table>

<hr style="margin:18px 0">

<form method="post">
<input type="text" name="name" placeholder="New category name" required>
<select name="type">
<option value="fixed">Fixed</option>
<option value="variable">Variable</option>
<option value="saving">Saving</option>
</select>
<button name="create" class="btn primary">Add</button>
</form>

</div>
</div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
