#include <sys/types.h>
#include <sys/ipc.h>
#include <sys/sem.h>
#include <sys/file.h>
#include <sys/errno.h>
#include <unistd.h>
#include <string.h>
#include <stdio.h>

#define KEY (9243)

void
usage( const char* cmd )
{
  printf( "%s usage:  %s maillist-name\n", cmd, cmd );
}

int
main( int argc, char** argv )
{
  /* the --quiet option means there is no return expected - it also means the owner won't get an e-mail */
  static const char* cmdfmt = "/var/mailman/bin/rmlist -a %s";
  char cmd[256];
  char line[256];
	char buf[256];
  FILE* cmdpipe, * aliases, * tmp;
	short done;
	int id;
	struct sembuf operations[1];
	int retval;
	
	union semun
		{
			int val;
			struct semid_ds *buf;
			ushort * array;
		}argument;
	
	
  if ( 2 != argc )
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

	operations[0].sem_op = 1; /* prepare to unlock the semaphore */
	
	sprintf( cmd, cmdfmt, argv[1] );
	if ( NULL == (cmdpipe = popen( cmd, "r" )) )
		{
			printf( "Unable to exec %s (errno was %d)\n", cmd, errno );
			retval = semop(id, operations, 1);
			return 1;
		}
	pclose( cmdpipe );

	if ( NULL == (tmp = fopen( "/tmp/aliases", "w" )) )
		{
			printf( "Unable to open /etc/aliases (errno was %d)\n", errno );
			retval = semop(id, operations, 1);
			return 1;
		}
	
	flock( (int)tmp, LOCK_EX );
	
	if ( NULL == (aliases = fopen( "/etc/aliases", "r" )) )
		{
			printf( "Unable to open /etc/aliases (errno was %d)\n", errno );
			retval = semop(id, operations, 1);
			return 1;
		}
	
	done = 0;
	while ( NULL != fgets( line, 255, aliases ) )
		{
			sprintf(buf,"## %s mailing list\n",argv[1]); 
			if (0 == done && 0 == strcmp(line,buf))
				{
					while ( 0 == done )
						{
							fgets( line, 255, aliases );
							if(0 == strcmp(line,"\n"))
								{
									fgets( line, 255, aliases );
									if(0 == strcmp(line,"\n")) /*if there is another newline we might as well take it out too.*/
										{
											fgets( line, 255, aliases );
										}
									done = 1;
								}
						}
				}
			fputs( line, tmp );
		}
		
		
	if( EOF == fclose( aliases ) )
		{
			printf( "There was an error closing the aliases file" );
		}

	flock( (int)tmp, LOCK_UN );
	
	if( EOF == fclose( tmp ) )
		{
			printf( "There was an error closing the aliases file" );
		}

/* move the new alias file to /etc */	
	if ( NULL == (cmdpipe = popen( "mv /tmp/aliases /etc/aliases", "r" )) )
		{
			printf( "Unable to exec 'mv /tmp/aliases /etc/aliases' (errno was %d)\n",  errno );
			retval = semop(id, operations, 1);
			return 1;
		}
	pclose( cmdpipe );

 /* Regenerate the aliases */

	if ( NULL == (cmdpipe = popen( "/usr/bin/newaliases", "r" )) )
		{
			printf( "Unable to refresh aliases (errno is %d)\n", errno );
		}
	
	pclose( cmdpipe );  /* We don't care about the output, we just don't want it going to the display. */

	retval = semop(id, operations, 1);
	
	return 0;
}
