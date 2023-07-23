#!/bin/bash
# Script to quickly create sub-theme.

echo '
+------------------------------------------------------------------------+
| With this script you could quickly create an CodeWrx project sub-theme     |
| In order to use this:                                                  |
| - codewrx_starter theme (this folder) should be in the contrib folder   |
+------------------------------------------------------------------------+
'
echo 'The machine name of your custom theme? [e.g. mycustom_codewrx_starter]'
read CUSTOM_CODEWRX_STARTER

echo 'Your theme name ? [e.g. My custom codewrx_starter]'
read CUSTOM_CODEWRX_STARTER_NAME

if [[ ! -e ../../custom ]]; then
    mkdir ../../custom
fi
cd ../../custom
cp -r ../custom/codewrx_starter $CUSTOM_CODEWRX_STARTER
cd $CUSTOM_CODEWRX_STARTER
for file in *codewrx_starter.*; do mv $file ${file//codewrx_starter/$CUSTOM_CODEWRX_STARTER}; done
for file in config/*/*codewrx_starter.*; do mv $file ${file//codewrx_starter/$CUSTOM_CODEWRX_STARTER}; done

# Remove create_subtheme.sh file, we do not need it in customized subtheme.
rm scripts/create_subtheme.sh

# mv {_,}$CUSTOM_BOOTSTRAP_SASS.theme
grep -Rl codewrx_starter .|xargs sed -i -e "s/codewrx_starter/$CUSTOM_CODEWRX_STARTER/"
sed -i -e "s/CodeWrx Starter Kit Subtheme/$CUSTOM_CODEWRX_STARTER_NAME/" $CUSTOM_CODEWRX_STARTER.info.yml
echo "# Check the themes/custom folder for your new sub-theme."
