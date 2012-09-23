#!/bin/bash

Dirlist=$(find /var/www/forks -maxdepth 1 -type d)
for direc in $Dirlist ; do
  if [ ! -d $direc"/.git" ]
  then
    continue
  fi
  cd $direc
  git checkout master
  git fetch upstream
  git merge upstream/master
  git push
done

