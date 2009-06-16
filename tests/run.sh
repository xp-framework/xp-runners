# Arguments
file=$1
on=$2

# Change to this script's directory
SELF=`dirname $0`
cd `realpath "$SELF"`

ENV="USE_XP=5.7.3"

# Read commands in test file, executing them one after each other
echo -n "==> ${file} "
while read line ; do
  case "$line" in
	CMD=*)
	  echo "<$ENV ${on}/${line#*=}>: " ;
	  eval $ENV ../${on}/${line#*=} 2>err 1>out ;
	  EXITCODE=$? ;
	;;

	OUT.EXPECT=*)
	  grep -E "${line#*=}" out >/dev/null;
	  if [ 0 = $? ] ; then
	    echo "--> OK, $line" ;
	  else
	    echo "*** STDOUT: Expecting <${line#*=}>, have:" ; cat out ; echo ;
      fi
    ;;

	ERR.EXPECT=*)
	  grep -E "${line#*=}" err >/dev/null;
	  if [ 0 = $? ] ; then
	    echo "--> OK, $line" ;
	  else
	    echo "*** STDERR: Expecting <${line#*=}>, have:" ; cat err ; echo ;
      fi
    ;;

	EXIT.EXPECT=*)
	  if [ "${line#*=}" = "$EXITCODE" ] ; then
	    echo "--> OK, exitcode $EXITCODE" ;
	  else
	    echo "*** EXITCODE: Expecting <${line#*=}>, have: $EXITCODE";
      fi
    ;;

	INI=*)
	  cp ini/${line#*=}.ini xp.ini ;
    ;;

	ENV.*=*)
	  var=${line%=*} ;
	  ENV="$ENV ${var#*.}=${line#*=}"
    ;;

	*)
      echo "$line";
    ;;
  esac
done < `basename ${file}` ;
if [ -e xp.ini ] ; then rm xp.ini ; fi
echo
rm out err
