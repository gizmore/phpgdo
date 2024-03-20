<?php
echo "<?php\n";
?>
function isPHPVersionCompatible()
{
    return (PHP_VERSION_ID >= 80200);
}

function isGitInstalled()
{
    $output = shell_exec('git --version');
    return (strpos($output, 'git version') !== false);
}

function isWindows()
{
    if (str_starts_with(substr(PHP_OS, 0, 3)) === 'WIN')
    {
        return true;
    }
    else
    {
        return false;
    }
}

function writeBat()
{
    $phpgdo = getcwd()."\\phpgdo\\";
    $bat = <<<EOB
setx /M PATH "%PATH%;$phpgdo"
EOB;

    file_put_contents("phpgdo.path.bat", $bat);
}

function setPath()
{
    if (isWindows())
    {
        writeBat();
        $output = shell_exec('runas /user:Administrator phpgdo.path.bat');
    }
    else
    {
        echo ""
    }

}



if (!isPHPVersionCompatible())
{
    echo "You need at least PHP8.2 to install phpgdo\n";
    die(-1);
}

if (!isGitInstalled())
{
    echo "You need GIT to install phpgdo\n";
    if (isWindows())
    {
        echo "On Windows, please install and use https://git-scm.com/download/win\nThen try again!\n";
    }
    die(-1);
}

echo "Cloning phpgdo core repository with git clone --recursive https://github.com/gizmore/phpgdo ...\n";
system("git clone --recursive https://github.com/gizmore/phpgdo");

echo "Setting the PATH environment variable to include phpgdo/bin ...\n";
//setPath();

