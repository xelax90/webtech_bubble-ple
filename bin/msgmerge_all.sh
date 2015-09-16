#!/bin/sh
apppath="`dirname \"$0\"`/.."

echo "Updating PO files using POT file"

for mod in $( ls ${apppath}"/module" ); do
	echo "  "$mod
	for i in $(find ${apppath}"/module/"${mod}"/language" -type f -name '*.po'); do
		filename="${i%.*}";
		printf "    "$(basename $filename)" "
		msgmerge \
			--update \
			--backup=simple\
			$i \
			${apppath}"/module/"${mod}"/language/messages.pot"
	done
done