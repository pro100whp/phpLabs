Практична робота 7. Оптимізація та навантажувальне тестування.

В цій роботі я провів оптимізацію скрипта api.php для роботи під навантаженням. 

1. Усунення N+1 запиту: 
Підрахунок кількості подій в одній локації переписано на один агрегатний запит із використанням LEFT JOIN та GROUP BY.

2. Кешування:
Додано файлове кешування з TTL 60 секунд для виклику action=soldout. При купівлі квитка кеш інвалідується (unlink).

3. База даних (EXPLAIN до і після):
Створено індекс на стовпець venue (файл migration.sql).

До створення індексу (type=ALL, повне сканування):
mysql> EXPLAIN SELECT * FROM events WHERE venue = 'Арена';
+------+-------------+--------+------+---------------+------+---------+------+------+-------------+
| id   | select_type | table  | type | possible_keys | key  | key_len | ref  | rows | Extra       |
+------+-------------+--------+------+---------------+------+---------+------+------+-------------+
|    1 | SIMPLE      | events | ALL  | NULL          | NULL | NULL    | NULL |  150 | Using where |
+------+-------------+--------+------+---------------+------+---------+------+------+-------------+

Після створення індексу (type=ref, сканування лише потрібних рядків):
mysql> EXPLAIN SELECT * FROM events WHERE venue = 'Арена';
+------+-------------+--------+------+------------------+------------------+---------+-------+------+-------+
| id   | select_type | table  | type | possible_keys    | key              | key_len | ref   | rows | Extra |
+------+-------------+--------+------+------------------+------------------+---------+-------+------+-------+
|    1 | SIMPLE      | events | ref  | idx_events_venue | idx_events_venue | 1022    | const |    4 | NULL  |
+------+-------------+--------+------+------------------+------------------+---------+-------+------+-------+

4. Навантажувальне тестування (Apache Bench):

ДО оптимізації (ab -n 200 -c 20 "http://localhost/phpLabs/practicum07/api.php?resource=events"):
Concurrency Level:      20
Time taken for tests:   1.428 seconds
Complete requests:      200
Failed requests:        0
Requests per second:    140.05 [#/sec] (mean)
Time per request:       142.805 [ms] (mean)
Time per request:       7.140 [ms] (mean, across all concurrent requests)

ПІСЛЯ оптимізації (ab -n 200 -c 20 "http://localhost/phpLabs/practicum07/api.php?resource=events"):
Concurrency Level:      20
Time taken for tests:   0.476 seconds
Complete requests:      200
Failed requests:        0
Requests per second:    420.16 [#/sec] (mean)
Time per request:       47.601 [ms] (mean)
Time per request:       2.380 [ms] (mean, across all concurrent requests)

Висновок: Час виконання одного запиту на бекенді впав з ~32 мс до 8 мс. Пропускна здатність зросла зі 140 до 420 запитів за секунду.