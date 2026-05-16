curl --request GET \
	--url 'https://booking-com15.p.rapidapi.com/api/v1/cars/searchCarRentals?pick_up_latitude=40.6397018432617&pick_up_longitude=-73.7791976928711&drop_off_latitude=40.6397018432617&drop_off_longitude=-73.7791976928711&pick_up_time=10%3A00&drop_off_time=10%3A00&driver_age=30&currency_code=USD&location=US' \
	--header 'Content-Type: application/json' \
	--header 'x-rapidapi-host: booking-com15.p.rapidapi.com' \
	--header 'x-rapidapi-key: 916d8af812msh70331aa50cb5dccp1e3dbbjsnd286759a11c8'


    curl --request GET \
	--url 'https://tripadvisor16.p.rapidapi.com/api/v1/restaurant/searchRestaurants?locationId=304554' \
	--header 'Content-Type: application/json' \
	--header 'x-rapidapi-host: tripadvisor16.p.rapidapi.com' \
	--header 'x-rapidapi-key: 916d8af812msh70331aa50cb5dccp1e3dbbjsnd286759a11c8'

    curl --request POST \
	--url https://ai-trip-planner.p.rapidapi.com/detailed-plan \
	--header 'Content-Type: application/json' \
	--header 'x-rapidapi-host: ai-trip-planner.p.rapidapi.com' \
	--header 'x-rapidapi-key: 916d8af812msh70331aa50cb5dccp1e3dbbjsnd286759a11c8' \
	--data '{"days":3,"destination":"London","interests":["fine dining","cuisine"],"budget":"medium","travelMode":"public transport"}'


    curl --request POST \
	--url 'https://travel-advisor.p.rapidapi.com/answers/v2/list?currency=USD&units=km&lang=en_US' \
	--header 'Content-Type: application/json' \
	--header 'x-rapidapi-host: travel-advisor.p.rapidapi.com' \
	--header 'x-rapidapi-key: 916d8af812msh70331aa50cb5dccp1e3dbbjsnd286759a11c8' \
	--data '{"contentType":"hotel","contentId":"4172546","questionId":"8393250","pagee":0,"updateToken":""}'


    curl --request POST \
	--url 'https://travel-guide-api-city-guide-top-places.p.rapidapi.com/check?noqueue=1' \
	--header 'Content-Type: application/json' \
	--header 'x-rapidapi-host: travel-guide-api-city-guide-top-places.p.rapidapi.com' \
	--header 'x-rapidapi-key: 916d8af812msh70331aa50cb5dccp1e3dbbjsnd286759a11c8' \
	--data '{"region":"London","language":"en","interests":["historical","cultural","food"]}'

    curl --request POST \
	--url https://travelchat-ai.p.rapidapi.com/travelchatAI \
	--header 'Content-Type: application/json' \
	--header 'x-rapidapi-host: travelchat-ai.p.rapidapi.com' \
	--header 'x-rapidapi-key: 916d8af812msh70331aa50cb5dccp1e3dbbjsnd286759a11c8' \
	--data '{"message":"Tell me best destinations and places for Paris"}'


    curl --request GET \
	--url https://airline-travel.p.rapidapi.com/ \
	--header 'Content-Type: application/json' \
	--header 'x-rapidapi-host: airline-travel.p.rapidapi.com' \
	--header 'x-rapidapi-key: 916d8af812msh70331aa50cb5dccp1e3dbbjsnd286759a11c8'



    curl --request GET \
	--url 'https://skedgo-tripgo-v1.p.rapidapi.com/locations.json?includeRoutes=false&modes=%5B%5D&includeDropOffOnly=false&strictModeMatch=true&includeChildren=false&sortedByProximity=false' \
	--header 'Content-Type: application/json' \
	--header 'x-rapidapi-host: skedgo-tripgo-v1.p.rapidapi.com' \
	--header 'x-rapidapi-key: 916d8af812msh70331aa50cb5dccp1e3dbbjsnd286759a11c8'


    