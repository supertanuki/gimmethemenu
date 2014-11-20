<?php
/**
 * Changelog.php permet de générer un changelog complet à partir du principe
 * de deploiement itn
 * Il génère un fichier html
 */

class Changlelog
{
    const DEFAULT_DESTINATION = '%s/web/index.html';
    const DEPLOIEMENT_PATTERN = '%s/%s/deploiement';

    const GIT_LOG_FORMAT = '%h|%cn|%ce|%ct|%s';
    const GIT_LOG_FIELD_SEPARATOR = '|';
    const GIT_LOG_COMMAND = 'git log --no-merges --pretty=format:\'%s\' %s';
    const GIT_LOG_INTER = '%s..%s';

    const INDEX_HASH = 0;
    const INDEX_AUTHOR = 1;
    const INDEX_MAIL = 2;
    const INDEX_TIME = 3;
    const INDEX_SUBJECT = 4;
    const INDEX_HISTORY = 5;

    const REDMINE_BASE = 'http://redmine.itnetwork.fr';
    const REDMINE_ISSUE = '%s/issues/%s';

    const TEMPLATE_HEAD = <<< EOF
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Changelog</title>
        <!-- Bootstrap -->
        <link href="./bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body {padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */ }
        </style>
        <link href="./bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
    </head>
    <body>
    <div class="container">
EOF;

    const TEMPLATE_BOTTOM = <<< EOF
        </div>
        <script src="./jquery/jquery-min.js"></script>
        <script src="./bootstrap/js/bootstrap.min.js"></script>
    </body>
</html>
EOF;

    const TEMPLATE_ENV = "<h1>%s<a name=\"%s\" href=\"#%s\"><i class=\"icon-bookmark\"></i></a></h1>\n";
    const TEMPLATE_ENV_START = <<<EOF
<table class="table table-condensed">
<thead><th>date</th><th>commit</th><th>commentaire</th><th>auteur</th></thead>
EOF;
    const TEMPLATE_ENV_END = "</table>\n";

    const TEMPLATE_DEPL = null;
    const TEMPLATE_COMMIT = "<tr class=\"%s\"><td>%s</td><td><span class=\"label label-info\">%s</label></td><td>%s</td><td>%s</td></tr>\n";
    const TEMPLATE_COMMIT_START = null;
    const TEMPLATE_COMMIT_END = null;
    const TEMPLATE_LINK = ' <a href="%s"><span class="label label-info">%s</span></a>';
    const TEMPLATE_NO_COMMIT = null;

    const TEMPLATE_MENU_START = "<p>\n";
    const TEMPLATE_MENU = "<a href=\"#%s\"><span class=\"label label-info\">%s</label></a>\n";
    const TEMPLATE_MENU_END = "</p>\n";

    const DATE_FORMAT = 'Y-m-d H:i:s';

    protected $deploy;
    protected $destination;
    protected $history;

    /**
     * @param $deploy le chemin absolu vers le repertoire deploy du projet
     * @param $destination le fichier de génération (par defaut $deploy . 'web/index.html'
     */
    public function __construct($deploy, $destination = null)
    {
        $this->setDeploy($deploy);
        $this->setDestination($destination);
        $this->history = array();
    }

    public function setDeploy($deploy)
    {
        if ( is_dir($deploy) == false || is_readable($deploy) == false ) {
            throw new \InvalidArgumentException(sprintf('%s is not readable or is not a directory', $deploy));
        }

        $this->deploy = $deploy;
    }

    public function getDeploy()
    {
        return $this->deploy;
    }

    public function setDestination($destination = null)
    {
        if ($destination == null) {
            $destination = sprintf( self::DEFAULT_DESTINATION, $this->deploy );
        }

        if ( is_writable(dirname($destination)) == false ) {
            throw new \InvalidArgumentException(sprintf('%s is not writable', dirname($destination)));
        }

        if ( file_exists($destination) && is_writable($destination) == false ) {
            throw new \InvalidArgumentException(sprintf('%s is not writable', $destination));
        }

        $this->destination = $destination;
    }

    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * Va calculer le changelog pour tous les env detectés
     */
    public function changelog()
    {
        $glob_pattern = sprintf(self::DEPLOIEMENT_PATTERN, $this->getDeploy(), '*');
        $sscanf_pattern = str_replace( '*/deploiement', '%s', $glob_pattern );
        foreach ( glob( $glob_pattern ) as $filename ) {
            sscanf( dirname($filename), $sscanf_pattern, $env );
            $this->history[$env] = $this->changelogForEnv( $env );
        }

        $this->writeHtml();
    }

    /**
     * va calculer le changelog pour un env donné
     * @param $env environnement a calculer
     */
    protected function changelogForEnv($env)
    {
        echo sprintf("generation du changelog pour %s", $env);
        $deploiement_file = sprintf(self::DEPLOIEMENT_PATTERN, $this->getDeploy(), $env);
        $git_log = sprintf(self::GIT_LOG_COMMAND, self::GIT_LOG_FORMAT, '-- '. $deploiement_file);
        $commit_history = $this->listCommit( $git_log );
        $commit_history_lenght = count($commit_history);

        for ($i=0; $i<$commit_history_lenght; $i++) {
            $actual_commit = $commit_history[$i][self::INDEX_HASH];
            $previous_commit = $i<$commit_history_lenght-1 ? $commit_history[$i+1][self::INDEX_HASH] : null;
            $git_log = sprintf(
                self::GIT_LOG_COMMAND,
                self::GIT_LOG_FORMAT,
                $previous_commit != null ? sprintf(self::GIT_LOG_INTER, $previous_commit, $actual_commit) : $actual_commit
            );
            $commit_history[$i][self::INDEX_HISTORY] = $this->listCommit($git_log);
        }

        echo "\t[ OK ]\n";

        return $commit_history;
    }

    /**
     * permet de lister les commits entre 2 hashs
     * @param $git_log
     * @return array
     */
    protected function listCommit($git_log)
    {
        $commit_history = explode( "\n", shell_exec($git_log));
        $commit_history_length = count($commit_history);
        for ($i=0; $i<$commit_history_length; $i++) {
            if ($commit_history[$i] == null) {
                unset($commit_history[$i]);
                continue;
            }
            $commit_line = explode( self::GIT_LOG_FIELD_SEPARATOR, $commit_history[$i] );
            $commit_history[$i] = $commit_line;
        }

        return $commit_history;
    }

    protected function writeHtml()
    {
        $hdle = fopen( $this->destination, "w" );
        fwrite($hdle, self::TEMPLATE_HEAD);

        fwrite($hdle, self::TEMPLATE_MENU_START);
        foreach ($this->history as $env => $env_history) {
            fwrite($hdle, sprintf(self::TEMPLATE_MENU, $env, $env));
        }
        fwrite($hdle, self::TEMPLATE_MENU_END);

        foreach ($this->history as $env => $env_history) {
            fwrite($hdle, sprintf(self::TEMPLATE_ENV, $env, $env, $env));
            fwrite($hdle, self::TEMPLATE_ENV_START);
            foreach ($env_history as $deploiement) {
                fwrite($hdle,
                    sprintf(
                        self::TEMPLATE_DEPL,
                        date(self::DATE_FORMAT, $deploiement[self::INDEX_TIME]),
                        $deploiement[self::INDEX_AUTHOR]
                    )
                );
                if ( count($deploiement[self::INDEX_HISTORY]) > 0 ) {
                    fwrite($hdle, self::TEMPLATE_COMMIT_START);
                    $depl = true;
                    foreach ($deploiement[self::INDEX_HISTORY] as $commit_history) {

                        $message = $depl ? '<span class="label label-success">deploiement</span> ' : null;
                        $message .= $this->linkSubject(strip_tags($commit_history[self::INDEX_SUBJECT]));

                        fwrite($hdle,
                            sprintf(
                                self::TEMPLATE_COMMIT,
                                $depl ? 'success' : null,
                                date(self::DATE_FORMAT, $commit_history[self::INDEX_TIME]),
                                $commit_history[self::INDEX_HASH],
                                $message,
                                $commit_history[self::INDEX_AUTHOR]
                            )
                        );
                        $depl = false;
                    }
                    fwrite($hdle, self::TEMPLATE_COMMIT_END);
                } else {
                    fwrite($hdle, self::TEMPLATE_NO_COMMIT);
                }
            }
            fwrite($hdle, self::TEMPLATE_ENV_END);
        }
        fwrite($hdle, self::TEMPLATE_BOTTOM);
        fclose($hdle);
    }

    protected function linkSubject($subject)
    {
        return preg_replace(
            '/#(\d+)/',
            sprintf(
                self::TEMPLATE_LINK,
                sprintf(
                    self::REDMINE_ISSUE,
                    self::REDMINE_BASE,
                    '${1}'
                ),
                '#${1}'
            ),
            $subject
        );
    }

}

$changelog = new Changlelog( dirname(__FILE__) );
$changelog->changelog();
