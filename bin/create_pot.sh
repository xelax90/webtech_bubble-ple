#!/bin/sh
apppath="`dirname \"$0\"`/.."

echo "Generating POT file"

find ${apppath}"/module" ${apppath}"/config" -type f \( -name '*.php' -or -name '*.phtml' \) \
	| xargs \
		${apppath}"/vendor/azatoth/php-pgettext/php-xgettext" \
			--add-location \
			--keyword=translate \
			--from-code=UTF-8 \
			-o ${apppath}"/module/SkelletonApplication/language/messages.pot"
