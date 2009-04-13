testrun:
	@echo "~~ Running testsuite ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~"
	@-for i in `ls *.test` ; do                                                      \
	  echo -n "==> $$i " ;                                                           \
	  ENV="USE_XP=5.7.2" ;                                                           \
	  while read line ; do                                                           \
	    case "$$line" in                                                             \
	      CMD=*)                                                                     \
	        echo "<$$ENV $(TYPE)/$${line#*=}>: " ;                                   \
	        eval $$ENV ../$(TYPE)/$${line#*=} 2>err 1>out ;                          \
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
