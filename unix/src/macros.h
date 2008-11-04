#define EXEC(runner, includes, args)         \
  cmd=$(execute "$DIRNAME" runner includes); \
  IFS="|";                                   \
  $cmd args "$@";                            \

