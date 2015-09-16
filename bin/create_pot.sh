#!/bin/sh
apppath="`dirname \"$0\"`/.."

echo "Generating POT files"

if [ -d ${apppath}"/module/SkelletonApplication" ]; then
	echo "  SkelletonApplication"
	find ${apppath}"/module/SkelletonApplication" ${apppath}"/config" -type f \( -name '*.php' -or -name '*.phtml' \) \
		| xargs \
			${apppath}"/vendor/azatoth/php-pgettext/php-xgettext" \
				--add-location \
				--keyword=translate \
				--from-code=UTF-8 \
				-o ${apppath}"/module/SkelletonApplication/language/messages.pot"
fi

for mod in $( ls ${apppath}"/module" | grep -v "^SkelletonApplication$" ); do
	echo '  '$mod

	if [ ! -d ${apppath}"/module/"${mod}"/language" ]; then
		echo '    Creating language directory'
		mkdir -pv ${apppath}"/module/"${mod}"/language" | sed 's/^/      /'
	fi

	find ${apppath}"/module/"$mod -type f \( -name '*.php' -or -name '*.phtml' \) \
		| xargs \
			${apppath}"/vendor/azatoth/php-pgettext/php-xgettext" \
				--add-location \
				--keyword=translate \
				--from-code=UTF-8 \
				-o ${apppath}"/module/"${mod}"/language/messages.pot"
done

