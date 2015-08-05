#!/bin/sh
apppath="`dirname \"$0\"`/.."

echo "Generation POT file"

find ${apppath}"/vendor/xelax90/xelax-admin" -type f \( -name '*.php' -or -name '*.phtml' \) \
	| xargs \
		${apppath}"/vendor/azatoth/php-pgettext/php-xgettext" \
			--add-location \
			--keyword=translate \
			--from-code=UTF-8 \
			-o ${apppath}"/vendor/xelax90/xelax-admin/language/messages.pot"
