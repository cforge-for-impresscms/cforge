#include <sys/types.h>
#include <sys/ipc.h>
#include <sys/sem.h>
#include <sys/errno.h>
#include <unistd.h>
#include <string.h>

#include <stdio.h>

#define KEY (9243)

void
usage( const char* cmd )
{
  printf( "%s usage:  %s maillist-name maillist-owner-email maillist-password\n", cmd, cmd );
}

int
main( int argc, char** argv )
{
  /* the --quiet option means there is no return expected - it also means the owner won't get an e-mail */
  static const char* cmdfmt = "/var/mailman/bin/newlist --quiet %s %s %s";
  char cmd[256];
  char line[256];
  FILE* cmdpipe, * aliases;
  int content_found = 0;
	int id;
	struct sembuf operations[1];
	int retval;

	union semun
		{
			int val;
			struct semid_ds *buf;
			ushort * array;
		}argument;


  if ( 4 > argc )
    {
      usage( argv[0] );
      return 1;
    }

  if ( -1 == setuid( 0 ) )
    {
      printf( "Failed to setuid to root.\n" );
    }

	id = semget(KEY, 1, 0666);
	if(id<0)
		{
			argument.val=1;
			
			id = semget(KEY, 1, 0666 | IPC_CREAT);
			
			semctl(id, 0, SETVAL, argument);
			
		}
		
		
	operations[0].sem_num = 0;
	operations[0].sem_op = -1;
	operations[0].sem_flg = 0;
	
	retval = semop(id, operations, 1);
	
  sprintf( cmd, cmdfmt, argv[1], argv[2], argv[3] );
  if ( NULL == (cmdpipe = popen( cmd, "r" )) )
    {
      printf( "Unable to exec %s (errno was %d)\n", cmd, errno );
      return 1;
    }
 
  if ( NULL == (aliases = fopen( "/etc/aliases", "a" )) )
    {
      printf( "Unable to open /etc/aliases (errno was %d)\n", errno );
      pclose( cmdpipe );
      return 1;
    }
  fprintf( aliases, "\n" );

  content_found = 0;
  while ( fgets( line, 255, cmdpipe ) != NULL )
    {
      if ( strlen( argv[1] ) > strlen( line ) )
	{
	  if ( content_found )
	    break;
	  else
	    continue;
	}
      if ( line[0] == '#' ||
	   (0 == strncmp( line, argv[1], strlen( argv[1] ) )) )
	{
	  content_found = 1;
	  fprintf( aliases, "%s", line );
	}
    }
  fprintf( aliases, "\n" );

  fclose( aliases );
  pclose( cmdpipe );

  /* Regenerate the aliases */
  if ( NULL == (cmdpipe = popen( "/usr/bin/newaliases", "r" )) )
    {
      printf( "Unable to refresh aliases (errno is %d)\n", errno );
    }
  pclose( cmdpipe ); /* We don't care about the output, we just don't want it going to the display. */

	operations[0].sem_num = 0;
	operations[0].sem_op = 1;
	operations[0].sem_flg = 0;
	
	retval = semop(id, operations, 1);

  printf( " " ); /* Don't ask.  It won't work if this isn't there. */

  return 0;
}
