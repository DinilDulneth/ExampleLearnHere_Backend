<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Lesson</title>
</head>
<body>
<h2>New Lesson Form</h2>

<form id="lessonForm" action="insertLesson.php" method="POST" enctype="multipart/form-data">
    
    <!-- Name -->
    <label for="name">Lesson Name:</label>
    <input type="text" id="name" name="name" required maxlength="255" title="Please enter a lesson name (max 255 characters).">
    <br><br>

    <!-- Price -->
    <label for="price">Price (Rs.):</label>
    <input type="number" id="price" name="price" required min="1" max="10000" step="0.01" title="Please enter a valid price (1-10000).">
    <br><br>
    <!-- Description -->
    <label for="description">Description:</label>
    <textarea id="description" name="description" required maxlength="1000" title="Please provide a description (max 1000 characters)."></textarea>
    <br><br>

    <!-- Thumbnail Picture -->
    <label for="thumbnailPicture">Thumbnail Picture (Image should be less than 2MB):</label>
    <input type="file" id="thumbnailPicture" name="thumbnailPicture" accept="image/png, image/jpeg, image/jpg" require>
    <br><br>
    <button type="submit">Submit</button>
</form>

</body>
</html>