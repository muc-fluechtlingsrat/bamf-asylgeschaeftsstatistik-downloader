Um das auszuführen wird Perl gebraucht und die Mojolicious Bibliothek.
Letztere kann man entweder installieren mit 'cpan Mojolicious' oder
pre-packaged in Ubuntu mit 'apt-get install libmojolicious-perl'.

Am Anfang ist ein wenig Setup, da solltest Du bei Bedarf den Pfad ändern
zu dem File wo der letzte State gespeichert wird. Beim ersten Aufruf
wird dann der State dadrin gespeichert, bei weiteren Aufrufen mit dem
bisherigen State verglichen, der neue gespeichert und die Änderungen auf
STDOUT (UTF-8) ausgegeben.
