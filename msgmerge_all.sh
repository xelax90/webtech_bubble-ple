#!/bin/sh
scriptpath="`dirname \"$0\"`"

echo "Updating PO files using POT file"

for i in $(find ${scriptpath}"/module/SkelletonApplication/language" -type f -name '*.po')
do
	filename="${i%.*}";
	echo $(basename $filename)
	msgmerge \
		--update \
		--backup=simple\
		$i \
		${scriptpath}"/module/SkelletonApplication/language/messages.pot"
done