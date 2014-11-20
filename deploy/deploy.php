#!/usr/bin/env php
<?php

/**
 * Permet de preparer les fichiers en vue d'un déploiement
 */

function print_step($step)
{
    printf(str_pad($step, 75, '.'));
}

function print_step_ok()
{
    printf("\033[1;32m[OK]\033[0m\n");
}

$envs = array('qa', 'production');
$bdd_drop_option = 'bdd_drop';

$usage = sprintf( "usage : php %s %s <username> <message> [%s]\n", $argv[0], implode('|', $envs), $bdd_drop_option );

if ( count($argv) != 1 && (count($argv) < 2 || count($argv) > 5) ) {
    echo $usage;
    exit(1);
}

if (($pos = array_search('--skip-test', $argv))) {
    array_splice($argv, $pos, 1);
    $test = false;
} else {
    $test = true;
}

if ($test) {

    print_step("Grunt release en cours...");
    $grunt_report = null;
    $grunt_return = 0;
    exec('./node_modules/grunt/bin/grunt release', $grunt_report, $grunt_return);
    if ($grunt_return>0) {
        printf("\n%s\n", implode("\n",$grunt_report));
        die();
    }
    print_step_ok();

    print_step("Tests unitaires en cours...");
    $phpunit_report = null;
    $phpunit_return = 0;
    exec('phpunit -c app/', $phpunit_report, $phpunit_return);
    if ($phpunit_return>0) {
        printf("\n%s\n", implode("\n",$phpunit_report));
        die();
    }
    print_step_ok();

    printf("Votre host (%s): ",'http://ea');
    $host = ask('http://ea');
    print_step("Tests fonctionnels en cours...");
    $casper_report = null;
    $casper_return = 0;
    exec(sprintf('casperjs test tests/casper/suite --host="%s" --screenshot="/tmp/error.png" --includes=tests/casper/include/inc.js',$host), $casper_report, $casper_return);
    if ($casper_return>0) {
    printf("\n%s\n", implode("\n",$casper_report));
        exec('display /tmp/error.png');
        die();
    }
    print_step_ok();
}

function accr($str)
{
    $words = explode(' ', ucwords($str));
    $str = null;
    foreach ($words as $word) {
        $str .= substr($word, 0, 1 );
    }

    return $str;
}

function ask($default=null)
{
    $stdin = fopen('php://stdin', 'r');
    $response = trim(fgets($stdin));
    fclose($stdin);

    return $response != null ? $response : $default;
}

$current_branch = trim(shell_exec( 'git rev-parse --abbrev-ref HEAD'), "%s\n" );

if (count($argv) == 1) {
    $env = str_replace('q-a', 'qa', $current_branch);
    printf("Environnement (%s) défaut %s : ", implode('|', $envs), $env);
    $env = ask( $env );

    $username = accr(trim(shell_exec( 'git config user.name')));
    printf("Vos initiales, défaut %s : ", $username);
    $username = ask( $username );

    $message = null;
    do {
        printf("Votre message : ", $username);
        $message = ask();
    } while ( $message == null );

    echo "Remise à zéro de la base de donnés (oui|non) défaut non : ";
    $bdd_drop = ask( 'non' ) == 'oui' ? true : false;

} else {
    $env = $argv[1];
    $username = $argv[2];
    $message = $argv[3];
    $bdd_drop = false;
}

if ( in_array($env, $envs) == false ) {
    echo $usage;
    exit(1);
}

if ($username == null || $message == null) {
    echo $usage;
    exit(1);
}

if ( isset($argv[4]) ) {
    if ($argv[4] != $bdd_drop_option) {
        echo $usage;
        exit(1);
    }
    $bdd_drop = true;
}

echo "\n";

//je verifie si je suis dans la bonne branche
$branch = str_replace('qa', 'q-a', $env);

if ($branch != $current_branch) {
    echo sprintf( "La branche actuelle (%s) ne correspond à la branche à deployer (%s)\n", $current_branch, $branch );
    echo sprintf( "git checkout %s\n", $branch );
    exit(2);
}

//je genere mon message
$line = sprintf( "# %s : %s %s", $message, $username, date('Y m d') );

echo sprintf( "Deploiement demande pour l'env %s\n", $env );
echo $bdd_drop ? "- avec remise à zero de la base de données\n" : null;
echo sprintf("- avec le message suivant : %s\n", $line );

if ($bdd_drop) {

    echo "\n";
    echo "ATTENTION : vous avez demander une réinitialisation complete de la base de données\n";
    echo "L'opération va entièrement la supprimer. Etes-vous bien CERTAIN de vouloir faire celà ? (oui|non, défaut non) : ";
    $response = ask( 'non' );

    if ($response !== 'oui') {
        echo "Demande de déploiement annulée\n";
        exit(255);
    }

    if ($env == 'production' || $env == 'prod') {
        echo "\n";
        echo "ATTENTION : réinitialisation demandée sur la PRODUCTION\n";
        echo "Etes-vous bien VRAIMENT CERTAIN de vouloir faire celà ? (oui|non, défaut non) : ";
        $response = ask( 'non' );

        if ($response !== 'oui') {
            echo "Demande de déploiement annulée\n";
            exit(255);
        }
    }

    echo "\n";
}

$deploiement_file = sprintf( "%s/%s/deploiement", __DIR__, $env );
echo sprintf("Ajout du message dans le fichier %s\n", $deploiement_file );
file_put_contents( $deploiement_file, sprintf("%s\n%s", $line, file_get_contents($deploiement_file) ) );

if ($bdd_drop) {
    $bdd_drop_file = sprintf( "%s/%s/bdd_drop", __DIR__, $env );
    echo sprintf("Ajout du message dans le fichier %s\n", $bdd_drop_file );
    file_put_contents( $bdd_drop_file, sprintf("%s\n%s", $line, file_get_contents($bdd_drop_file) ) );
}

echo sprintf("git commit -am 'depl %s - %s'\n", $message, $env);
echo sprintf("git push origin %s\n", $branch);
