#!/bin/sh

AR="$1"
rm "$AR"

shift
for file in "$@" ; do
  if [ -f "$file" ] ; then
    printf -- "--%d:%s:--\n" `stat -c '%s' $file` $(basename $file)
    printf -- "--%d:%s:--\n" `stat -c '%s' $file` $(basename $file) >> $AR
    cat $file >> $AR
  fi
done
echo -n "===> Done: "
ls -al "$AR"
