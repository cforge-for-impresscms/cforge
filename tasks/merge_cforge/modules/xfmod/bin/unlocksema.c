#include <sys/types.h>
#include <sys/ipc.h>
#include <sys/sem.h>

#define KEY (9243)

/*
	If for some reason the semaphore gets off count, run this and it 
	will reset the semaphore to where it should be 

	compile using: gcc -o unlocksema unlocksema.c -lpthread -lrt 
*/

int
main( int argc, char** argv )
{
	int id;
	struct sembuf operations[1];
	int retval;
	
	union semun
		{
			int val;
			struct semid_ds *buf;
			ushort * array;
		}argument;

	argument.val=1;
	id = semget(KEY, 1, 0666 | IPC_CREAT);
	semctl(id, 0, SETVAL, argument);			

	return 0;
}