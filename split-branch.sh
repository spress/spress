#!/bin/bash
#
# This script is used to split the master version of spress/Spress
# into several independent repositories. This script is inspired
# by the split-branch of https://github.com/PaymentSuite/paymentsuite.
#
# Require: GIT tool +1.8
#
pushd /tmp
rm -rf spress-split.tmp
git clone git@github.com:spress/Spress.git spress-split.tmp

printf "Repository cloned.\n"

pushd spress-split.tmp

git subtree split --prefix=src/Core/ --branch split-branch
git checkout split-branch
git remote add rewrite git@github.com:spress/Spress-core.git

printf "Remote added.\n"

git push rewrite split-branch:master
git checkout master
git branch -D split-branch
git remote rm rewrite

rm -rf /tmp/spress-split.tmp
popd
popd
