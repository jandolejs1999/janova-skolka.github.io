<?php

namespace App;

use App\Content;
use App\Storage\Storage;
use App\Validator\ValidateException;
use Tracy\Debugger;
use Tracy\ILogger;

// ===== Autoloader knihoven a start Laděnky ====

require __DIR__ . '/../vendor/autoload.php';

Debugger::enable(Debugger::DETECT, __DIR__ . '/../log');

(new \Nette\Loaders\RobotLoader)->addDirectory(__DIR__ . '/../libs')
    ->setTempDirectory(__DIR__ . '/../temp/cache')->register();

// ===== Inicializace ===========================

$user = null;
$error = null;
$storage = new Storage(__DIR__ . '/../output');

$users = $storage->findKeys();

// ===== Aplikace ===============================

if (Helpers::isFormSent('registration-form')) {
    try {

        $user = new User(
            new Content\Username(Helpers::getFormValue('username')),
            new Content\Password(Helpers::getFormValue('password')),
            new Content\Name(Helpers::getFormValue('name'))
        );

        if (Helpers::isFilled(Helpers::getFormValue('phone'))) {
            $user->setPhone(new Content\Phone(Helpers::getFormValue('phone')));
        }

        if (Helpers::isFilled(Helpers::getFormValue('email'))) {
            $user->setEmail(new Content\Email(Helpers::getFormValue('email')));
            Mail\Mailer::sendMail($user->toArray());
        }

        $storage->save($user->getName(), $user->toArray(User::WITH_PASSWORD));

    } catch (Mail\MailerException $e) {
        Debugger::log('email_not_sent="' . $e->getMessage() . '"');
        $error = 'Email se nepovedlo odeslat z tohoto důvodu: ' . $e->getMessage();
    } catch (ValidateException $e) {
        $error = $e->getMessage();
    } catch (\Exception $e) {
        Debugger::log($e, ILogger::ERROR);
        $error = 'Omlouváme se, něco se pokazilo, zkuste to znovu později nebo nás kontaktujte na support@service.cz';
    }
}

$pageNum = null;
$pageAddress = $_SERVER['PHP_SELF'];
if ($pageAddress == "/www/index.php/add") {
    $pageNum = 'add';
} else {
    if ($pageAddress == "/www/index.php/show") {
        $pageNum = 'show';
    } else {
        $pageNum = '404';
    }
}

?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">

    <title>Janova školka HTML - Lekce 23</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css"
          integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ=="
          crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css"
          integrity="sha384-aUGj/X2zp5rLCbBxumKTCw2Z50WgIr1vs/PFN4praOTvYXWlVyh2UtNUU0KAUhAX" crossorigin="anonymous">
</head>
<body>
<div class="container">

    <div class="navButtons">
            <a class="btn btn-primary" href="/www/add">  <?php if ($pageNum == 'add') {
                echo '*';
            } ?>Registrace uživatele</a>
        <a class="btn btn-primary" href="/www/show"> <?php if ($pageNum == 'show') {
                echo '*';
            } ?>Seznam uživatelů</a>
    </div>

    <div class="jumbotron">

        <h1>Zkušební aplikace</h1>

        <?php if ($pageNum == 'show') {
            echo '<h2>Uživatelé</h2>';
            if ($users) {

                echo '<table class="table table-bordered table-hover">';

                foreach ($users as $user) {
                    echo '<tr>';
                    $data = $storage->getByKey($user);
                    echo '<td>' . (isset($data['name']) ? $data['name'] : "-") . '</td>';
                    echo '<td>' . (isset($data['username']) ? $data['username'] : "-") . '</td>';
                    echo '<td>' . (isset($data['phone']) ? $data['phone'] : "-") . '</td>';
                    echo '<td>' . (isset($data['mail']) ? $data['mail'] : "-") . '</td>';
                    echo '</tr>';
                }

                echo '</table>';
            } else {
                echo '<h4>žádný uživatel</h4>';
            }
        } ?>

        <?php if ($pageNum == 'add') : ?>
            <?php if ($user instanceof User): ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="alert alert-success" role="alert">
                            Registrace byla úspěšně dokončena.
                        </div>
                        <a class="btn btn-success" href="/www/add">Registrace dalšího uživatele</a>
                        <h3>Data z formuláře</h3>
                        <table class="table table-bordered">
                            <tr>
                                <th>Uživatelské jméno:</th>
                                <td>
                                    <?php echo Escape::html($user->getUsername()); ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Jméno:</th>
                                <td>
                                    <?php echo Escape::html($user->getName()); ?>
                                </td>
                            </tr>
                            <tr>
                                <?php if ($user->hasPhone()): ?>
                                <th>Telefon:</th>
                                <td>
                                    <?php echo Escape::html($user->getPhone()); ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <?php if ($user->hasEmail()): ?>
                                <tr>
                                    <th>Email:</th>
                                    <td>
                                        <?php echo Escape::html($user->getEmail()); ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <h3>Registrace uživatele</h3>
                <?php if ($error !== null): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form action="" method="post">
                    <div class="form-group">
                        <label for="name">Jméno *</label>
                        <input type="text" class="form-control" name="name" id="name"
                               value="<?php echo Escape::html(Helpers::getFormValue('name')); ?>" autocomplete="name">
                    </div>
                    <div class="form-group">
                        <label for="username">Uživatelské jméno *</label>
                        <input type="text" class="form-control" name="username" id="username"
                               value="<?php echo Escape::html(Helpers::getFormValue('username')); ?>" autocomplete="username">
                    </div>
                    <div class="form-group">
                        <label for="password">Heslo *</label>
                        <input type="password" class="form-control" name="password" id="password">
                    </div>
                    <div class="form-group">
                        <label for="phone">Telefon</label>
                        <input type="text" class="form-control" name="phone" id="phone"
                               value="<?php echo Escape::html(Helpers::getFormValue('phone')); ?>" autocomplete="tel-national">
                    </div>
                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input type="text" class="form-control" name="email" id="email"
                               value="<?php echo Escape::html(Helpers::getFormValue('email')); ?>" autocomplete="email">
                    </div>
                    <input type="hidden" name="action" value="registration-form">
                    <input type="submit" name="submit" value="Registrovat se" class="btn btn-primary">
                </form>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($pageNum == '404') {
            http_response_code(404);
            echo '<h2>Stránka nenalezena :(</h2>';
        } ?>
    </div>
</div>
</body>
</html>
