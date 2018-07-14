<?php

    $formValid = false;
    $errors = [];

    //form send
    if(isFormSent('registration-form')) {
        $name = getFormValue('name');
        validateRequired($name, 'Jméno') && validateName($name, 'Jméno');

        $phone = getFormValue('phone');
        validateRequired($phone, 'Telefon') && validatePhone($phone, 'Telefon');

        if(getFormValue('email')){
            if(!validateEmail(getFormValue("email"))){addError("Email byl vyplněn ale je neplatný."); }
        }
        if(count($errors) === 0) {
            $formValid = true;
        }
    }

    // === Pomocné funkce ===

    function escapeHtml($text){
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }

    function getFormValue($inputName, $default = '') {
        if(isset($_POST[$inputName])) {
            return $_POST[$inputName];
        }
        return $default;
    }

    function isFormSent($formName) {
        return getFormValue('action') === $formName;
    }

    function validateRequired($value, $title) {
        $isValid = ($value !== '');
        if(!$isValid) {
            addError("Pole $title není vyplněno, prosím, vyplňte jej.");
        }
        return $isValid;
    }

    function validateName($value, $title) {
        $isValid = !preg_match('/\d/', $value);
        if(!$isValid) {
            addError("Pole $title nesmí obsahovat číslo.");
        }
        return $isValid;
    }

    function validatePhone($value, $title) {
        $isValid = preg_match('/^ *(\d *){9}$/', $value);
        if(!$isValid) {
            addError("Pole $title musí obsahovat pouze 9 číslic (mezery jsou povoleny).");
        }
        return $isValid;
    }

    function validateEmail($value) {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    function addError($error) {
        global $errors;
        $errors[] = $error;
    }

?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">

    <title>Janova školka HTML - Lekce 15</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css" integrity="sha384-aUGj/X2zp5rLCbBxumKTCw2Z50WgIr1vs/PFN4praOTvYXWlVyh2UtNUU0KAUhAX" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <div class="jumbotron">
            <p><a href="../">« Další úkoly</a></p>
            <h1>Lekce 15</h1>

            <p><strong>Pokročilá validace formuláře</strong></p>
            <p>Tvůj úkol je:</p>
            <ul>
                <li>Vypsat zde jméno a telefon z formuláře při odeslání. Pokud se nic neodešle, tak schovat celou tabulku a místo ní napsat jen „Formulář nebyl odeslán“.</li>
                <li>Podmínky formuláře:
                    <ul>
                        <li>jméno i telefon jsou povinné a nesmí být prázdné; <strong>e-mail je dobrovolný</strong>,</li>
                        <li>jméno nesmí obsahovat číslo,</li>
                        <li>telefon musí obsahovat právě 9 čísel, libovolně prokládaných mezerami (např. <code>123456789</code> i <code>123 45 67 89</code>),</li>
                        <li><strong>e-mail musí být v platném tvaru</strong>.</li>
                    </ul>
                </li>
                <li>Pokud data z formuláře nesplňují všechny podmínky, tak se považuje za neodeslaný a je potřeba vyhodit uživateli chybovou hlášku.</li>
                <li>Chybová hláška musí být smysluplná, aby uživatel pochopil, co má špatně.</li>
            </ul>

            <div class="row">
                <div class="col-sm-12">
                    <h3>Data z formuláře</h3>
                    <?php if($formValid): ?>
                    <div class="alert alert-success" role="alert">
                        Formulář úspěšně odeslán
                    </div>
                    <table class="table table-bordered">
                        <tr>
                            <th>Jméno:</th>
                            <td>
                            <?php echo escapeHtml($_POST['name']); ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Telefon:</th>
                            <td>
                            <?php echo escapeHtml($_POST['phone']); ?>
                            </td>
                        </tr>
                        <?php if(validateEmail(getFormValue("email"))): ?>
                        <tr>
                            <th>Email:</th>
                            <td>
                            <?php echo escapeHtml($_POST['email']); ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </table>
                    <?php else: ?>
                    <div class="alert alert-info" role="alert">
                        Formulář nebyl odeslán
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <h3>Formulář</h3>
            <?php if(count($errors)): ?>
                <div class="alert alert-danger" role="alert">
                    <strong>Formulář nelze odeslat, protože obsahuje tyto chyby:</strong>
                    <ul>
                        <?php foreach ($errors as $error) {
                            echo "<li>$error</li>";
                        } ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="" method="post">
                <div class="form-group">
                    <label for="name">Jméno *</label>
                    <input type="text" class="form-control" name="name" id="name" value="<?php echo escapeHtml(getFormValue('name')); ?>">
                </div>
                <div class="form-group">
                    <label for="phone">Telefon *</label>
                    <input type="text" class="form-control" name="phone" id="phone" value="<?php echo escapeHtml(getFormValue('phone')); ?>">
                </div>
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="text" class="form-control" name="email" id="email" value="<?php echo escapeHtml(getFormValue('email')); ?>">
                </div>
                <input type="hidden" name="action" value="registration-form">
                <input type="submit" name="submit" value="Odeslat" class="btn btn-primary">
            </form>
        <div style="margin-top: 2em;"><small>Pomůcka: <code>filter_var()</code>.</small></div>
        </div>
    </div>
</body>
</html>