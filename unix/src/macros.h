#define EXEC(runner, includes, args)         \
  base="$DIRNAME"                            \
  RUNNER=runner                              \
  INCLUDE=includes                           \
  xppath="$DIRNAME"                          \
  ARGS=args                                  \
  IFS="|";                                   
