#!/bin/sh
apppath="`dirname \"$0\"`/.."

echo "Creating MO files from PO translations"

for mod in $( ls ${apppath}"/module" ); do
	echo "  "$mod
	for i in ${apppath}"/module/"${mod}"/language/*.po"; do
		filename="${i%.*}";
		echo "    "$(basename $filename)
		msgfmt -o ${filename}".mo" ${i}
	done
done
