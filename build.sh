#! /bin/sh
# business install 
# script name : build.sh
#set -eu -o pipefail

SELF_NAME="build.sh"
VERSION="1.0.0"
FILE_NAME="10000km"
BUILD_NAME="./output"
CURR_DIR=`pwd`

echo "start building ..."

find ./ -regex ".*\.svn$" -type d | xargs rm -r
if [ $? -ne 0 ]; then 
	echo "remove .svn dirs failed!"
	exit 1
fi

rm -rf $BUILD_NAME

objs=`ls .`

mkdir $BUILD_NAME
if [ $? -ne 0 ]; then 
	echo "create dir \"$BUILD_NAME\" failed!"
	exit 1
fi

mv $objs $BUILD_NAME/
if [ $? -ne 0 ]; then 
	echo "move files to dir \"$BUILD_NAME\" failed!"
	exit 2
fi

chown root:root $BUILD_NAME/ -R
chmod 755 $BUILD_NAME/ -R

echo "$BUILD_NAME has been created at dir $CURR_DIR/output/ !"
echo "build succeed!"
exit 0
