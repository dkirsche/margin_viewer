CREATE proc savesales(@year smallint,@period tinyint,@datestart smalldatetime,@dateend smalldatetime)
as
declare @charges numeric(12,2)
declare @refunds numeric(12,2)
declare @refunds_temp numeric(12,2)
declare @shipping numeric(12,2)
declare @productcost numeric(12,2)
declare @btlCount int
select @charges=sum(c.amount) from charges c join billing on c.ordernum=billing.ordernum where c.type='sale' and c.approved='approved' and billing.date>=@datestart and billing.date<@dateend and billing.status=4
select @refunds_temp=sum(x.amount) from charges x join billing on x.ordernum=billing.ordernum where x.type='credit' and x.approved='approved' and billing.date>=@datestart and billing.date<@dateend and billing.status=4

set @refunds=0

if @refunds_temp>0

	set @refunds=@refunds_temp
select @shipping=sum(p.cost) from homs_packages p join homs_shipments s on p.shipmentid=s.id join billing on billing.ordernum=s.ordernum where  billing.date>=@datestart and billing.date<@dateend and billing.status=4
select @btlCount=sum(contents.quantity) from homs_contents contents join homs_shipments s on contents.shipmentid=s.id join billing on billing.ordernum=s.ordernum where  billing.date>=@datestart and billing.date<@dateend and billing.status=4
select @productcost=sum(orders.inventorycost*((orders.btlqty/convert(numeric(5,2),orders.btlpercase))+orders.caseqty)) from orders join billing on orders.ordernum=billing.ordernum where billing.date>=@datestart and billing.date<@dateend and billing.status=4 
if exists(select period from salesfigures where year=@year and period=@period)
	update salesfigures set charges=@charges,refunds=@refunds,shippingcost=@shipping,productcost=@productcost,btlcount=@btlCount,datestart=@datestart,dateend=@dateend
	where year=@year and period=@period
	
else
	insert into salesfigures (year,period,charges,refunds,shippingcost,productcost,btlcount,datestart,dateend) 
	values(@year,@period,@charges,@refunds,@shipping,@productcost,@btlCount,@datestart,@dateend)
	
	
	
	