-- Group by price ranges
-- select t.id, t.ranges as ranges, t.name, t.price, count(*) as 'number of users'
select id, count(*) AS num
from 
(  select case    
    when price between 0 and 200 then ' 0-200'  
    when price between 200 and 400 then ' 200-400'  
    when price between 400 and 600 then ' 400-600'  
    when price between 600 and 800 then ' 600-800'
    else '> 800'   
     end as ranges
	  -- ,p.name, p.price
  from products)  as summary
group by ranges, id
-- order by price asc