#!/bin/sh
apppath="`dirname \"$0\"`/.."

echo "Creating MO files from PO translations"

for i in ${apppath}/module/SkelletonApplication/language/*.po
do
	filename="${i%.*}";
	echo $(basename $filename)
	msgfmt -o ${filename}".mo" ${i}
done
