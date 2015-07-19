#!/bin/sh
scriptpath="`dirname \"$0\"`"

echo "Generation POT file"

find ${scriptpath}"/module" -type f \( -name '*.php' -or -name '*.phtml' \) \
	| xargs \
		${scriptpath}"/vendor/azatoth/php-pgettext/php-xgettext" \
			--add-location \
			--keyword=translate \
			--from-code=UTF-8 \
			-o ${scriptpath}"/module/SkelletonApplication/language/messages.pot"
