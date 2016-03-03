-- example group
SELECT agegroup, count(*) AS total 
					FROM (SELECT
						  CASE WHEN price BETWEEN 0 AND 9 THEN '0 to 9'
						  WHEN price BETWEEN 10 and 19 THEN '10 to 19'
						  WHEN price BETWEEN 20 and 29 THEN '20 to 29'
						  WHEN price BETWEEN 30 and 39 THEN '30 to 39'
						  WHEN price BETWEEN 40 and 49 THEN '40 to 49'
						  WHEN price BETWEEN 50 and 59 THEN '50 to 59'
						  WHEN price >= 60 THEN '60 +' END AS agegroup
						  FROM products) entries
					GROUP BY agegroup