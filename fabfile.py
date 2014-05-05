from fabric.api import local,sudo,run
from fabric.context_managers import cd

targetDir = "/Library/Server/Web/Data/Sites/schalchlab.unige.ch/labdb"

def deploy():
    with cd("/Users/schalch/_Programming/labdb/html"):
        run("git archive -o html.zip HEAD")
        sudo("unzip -f html.zip -d %s" % (targetDir))
        run("rm html.zip")
