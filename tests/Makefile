testrun:
	@echo "~~ Running testsuite ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~"
	@-for i in `ls *.test` ; do                                                      \
	  echo -n "==> $$i " ;                                                           \
	  ENV="USE_XP=5.7.2" ;                                                           \
	  if [ -e ../$(TYPE)/xp.ini ] ; then rm ../$(TYPE)/xp.ini ; fi ;                 \
	  while read line ; do                                                           \
	    case "$$line" in                                                             \
	      CMD=*)                                                                     \
	        echo "<$$ENV $(TYPE)/$${line#*=}>: " ;                                   \
	        eval $$ENV ../$(TYPE)/$${line#*=} 2>err 1>out ;                          \
	        EXITCODE=$$? ;                                                           \
	      ;;                                                                         \
	                                                                                 \
	      OUT.EXPECT=*)                                                              \
	        grep -E "$${line#*=}" out >/dev/null;                                    \
	        if [ 0 == $$? ] ; then                                                   \
	          echo "--> OK, $$line" ;                                                \
	        else                                                                     \
	          echo "*** STDOUT: Expecting <$${line#*=}>, have:" ; cat out ; echo ;   \
            fi                                                                       \
          ;;                                                                         \
	                                                                                 \
	      ERR.EXPECT=*)                                                              \
	        grep -E "$${line#*=}" err >/dev/null;                                    \
	        if [ 0 == $$? ] ; then                                                   \
	          echo "--> OK, $$line" ;                                                \
	        else                                                                     \
	          echo "*** STDERR: Expecting <$${line#*=}>, have:" ; cat err ; echo ;   \
            fi                                                                       \
          ;;                                                                         \
	                                                                                 \
	      EXIT.EXPECT=*)                                                             \
	        if [ "$${line#*=}" = "$$EXITCODE" ] ; then                               \
	          echo "--> OK, exitcode $$EXITCODE" ;                                   \
	        else                                                                     \
	          echo "*** EXITCODE: Expecting <$${line#*=}>, have: $$EXITCODE";        \
            fi                                                                       \
          ;;                                                                         \
	                                                                                 \
	      INI=*)                                                                     \
	        cp ini/$${line#*=}.ini ../$(TYPE)/xp.ini ;                               \
          ;;                                                                         \
	                                                                                 \
	      ENV.*=*)                                                                   \
	        var=$${line%=*} ;                                                        \
	        ENV="$$ENV $${var#*.}=$${line#*=}"                                       \
          ;;                                                                         \
	                                                                                 \
	      *)                                                                         \
            echo "$$line";                                                           \
          ;;                                                                         \
	    esac                                                                         \
	  done < $$i ;                                                                   \
	  echo ;                                                                         \
	done
	@echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~"
	rm out err
