<!--------w-----------

    Project 4
    Name: Gabriel Datuin
    Date: 
    Description: Displays the contact page of a clothing website.

--------------------->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Contact Us - LUMi</title>
    <link rel="stylesheet" href="styles.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bruno+Ace&family=Fugaz+One&family=Russo+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Days+One&display=swap" rel="stylesheet">
    <script src="form-validation.js"></script>
    <script src="https://static.elfsight.com/platform/platform.js" data-use-service-core defer></script>
    <link rel="shortcut icon" href="#">
</head>

<body id="contact-us">
    
    <div class="elfsight-app-4114d580-7b3f-4432-b30a-d4699aac173d"></div>

    <?php include 'header.php'; ?>
    

<main>
    <div id="heading">
        <h1>Questions or Feedback? Let us know.</h1>

    </div>


<form id="form" action="index.php" method="post">
<ul>
    <li>
        <label for="fullname">Full Name</label>
        <input id="fullname" name="fullname" type="text" />
        <p class="form error" id="fullname_error">* Required field</p>
    </li>
    <li>
    <label for="phone">Phone Number</label>
        <input id="phone" name="phone" type="tel" />
        <p class="form error" id="phone_error">* Required field</p>
        <p class="form error" id="phoneformat_error">* Invalid phone number</p>
    </li>
        <li>
            <label for="email">Email</label>
            <input id="email" name="email" type="text" />
            <p class="form error" id="email_error">* Required field</p>
            <p class="form error" id="emailformat_error">
                * Invalid email address
            </p>
        </li>
        <li>
            <label for="comments"></label>
            <textarea name="comments" id="comments" cols="80" rows="15" placeholder="Enter your questions or comments..."></textarea>
            
        </li>
</ul>

<p class="center">
<button type="submit" id="submit" class="formButton">Submit</button>
<button type="reset" id="clear" class="formButton">Reset</button>
</p>

</form>    

</main>

<?php include 'footer.php'; ?>

</body>
</html>