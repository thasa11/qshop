SELECT *, count(*) AS grouptotal FROM ( select *
         , case  when price <100 then 'Cheaper than 100€' 
			when price between 100 and 200 then 'Between 100€ and 200€' 
			when price between 200 and 300 then 'Between 200€ and 300€' 
			when price between 300 and 400 then 'Between 300€ and 400€' 
			when price between 400 and 500 then 'Between 400€ and 500€' 
			when price >500 then 'More that 500€' end as pricerange
        from basket left join products on basket.pid = products.id
        left join stock on products.id = stock.productid
        WHERE price  LIKE '%' AND name LIKE '%' LIMIT 0,1000) tbl
        group by pricerange, tbl.id
        order by price asc