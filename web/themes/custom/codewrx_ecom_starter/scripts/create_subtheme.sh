#!/bin/bash
# Script to quickly create sub-theme.

echo '
+------------------------------------------------------------------------+
| With this script you could quickly create a CodeWrx project sub-theme     |
| In order to use this:                                                  |
| - codewrx_ecom_starter theme (this folder) should be in the custom folder   |
+------------------------------------------------------------------------+
'
echo 'The machine name of your custom theme? [e.g. mycustom_codewrx_ecom_starter]'
read CUSTOM_CODEWRX_ECOM_STARTER

echo 'Your theme name ? [e.g. My custom codewrx_ecom_starter]'
read CUSTOM_CODEWRX_ECOM_STARTER_NAME

if [[ ! -e ../../custom ]]; then
    mkdir ../../custom
fi
cd ../../custom
cp -r ../custom/codewrx_ecom_starter $CUSTOM_CODEWRX_ECOM_STARTER
cd $CUSTOM_CODEWRX_ECOM_STARTER
for file in *codewrx_ecom_starter.*; do mv $file ${file//codewrx_ecom_starter/$CUSTOM_CODEWRX_ECOM_STARTER}; done
for file in config/*/*codewrx_ecom_starter.*; do mv $file ${file//codewrx_ecom_starter/$CUSTOM_CODEWRX_ECOM_STARTER}; done

# Remove create_subtheme.sh file, we do not need it in customized subtheme.
rm scripts/create_subtheme.sh

# mv {_,}$CUSTOM_BOOTSTRAP_SASS.theme
grep -Rl codewrx_ecom_starter .|xargs sed -i -e "s/codewrx_ecom_starter/$CUSTOM_CODEWRX_ECOM_STARTER/"
sed -i -e "s/CodeWrx Ecom Starter Kit Subtheme/$CUSTOM_CODEWRX_ECOM_STARTER_NAME/" $CUSTOM_CODEWRX_ECOM_STARTER.info.yml
echo "# Check the themes/custom folder for your new sub-theme."
