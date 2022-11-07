exit

repo init   -u git@github.com:bertozzi/finforepo.git
repo sync
repo forall -c 'git checkout 2022'

# PER NUOVE BRANCH
# git checkout -b 2022
# git push --set-upstream origin 2022
