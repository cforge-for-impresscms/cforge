CC=gcc -Wall -pedantic

all: newmailinglist rmmailinglist

clean: 
	rm newmailinglist
	rm rmmailinglist
	
newmailinglist:	newmailinglist.c
	$(CC) $(CFLAGS) -o newmailinglist newmailinglist.c -lpthread -lrt
	chmod 4111 newmailinglist
	chown root.root newmailinglist

rmmailinglist: rmmailinglist.c
	$(CC) $(CFLAGS) -o rmmailinglist rmmailinglist.c -lpthread -lrt
	chmod 4111 rmmailinglist
	chown root.root rmmailinglist