#!/bin/sh

AR="$1"
shift
for file in "$@" ; do
  if [ -f "$file" ] ; then
    echo "---> $file"
    printf -- "--%d:%s:--\n" `stat -c '%s' $file` $(dirname $file) >> $AR
    cat $file >> $AR
  fi
done
echo -n "===> Done: "
ls -al "$AR"
