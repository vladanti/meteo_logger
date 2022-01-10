#!/usr/bin/python3

import os
import schedule
import time
import datetime
from WS_UMB import WS_UMB # module for UMB protocol communication
import minimalmodbus # module for Modbus protocol communication
# import json # http://www.json.org/ # https://jsonlint.com/ # https://docs.python.org/3/library/json.html
import pymongo # module for MongoDB

# connect to local database
localdbsrv = pymongo.MongoClient("mongodb://localhost:27017/") # connect to local MongoDB Server
localdb = localdbsrv["meteo"] # database name
localcol = localdb["data"] # collection

# connect to remote database
try:
	remotedbsrv = pymongo.MongoClient("mongodb://meteo.physgeo.com:7443/") # connect to remote MongoDB Server
	remotedb = remotedbsrv["meteo"] # database name
	remotecol = remotedb["data"] # collection
except Exception:
	print('Error. Coud not connect to remote database')
else:
	print('Sucsess. Connected to remote database')


#CONVERT SECTION
def wind_dir_convert(azimuth, windspeed):
	if azimuth > 348.75 and azimuth < 360 or azimuth > 0 and azimuth < 11.25: # Конвертируем азимут ветра в направление компас
		windrose = "N"
	elif azimuth > 11.25 and azimuth < 33.75:
		windrose = "NNE"
	elif azimuth > 33.75 and azimuth < 56.25:
		windrose = "NE"
	elif azimuth > 56.25 and azimuth < 78.75:
		windrose = "ENE"
	elif azimuth > 78.75 and azimuth < 101.25:
		windrose = "E"
	elif azimuth > 101.25 and azimuth < 123.75:
		windrose = "ESE"
	elif azimuth > 123.75 and azimuth < 146.25:
		windrose = "SE"
	elif azimuth > 146.25 and azimuth < 168.75:
		windrose = "SSE"
	elif azimuth > 168.75 and azimuth < 191.25:
		windrose = "S"
	elif azimuth > 191.25 and azimuth < 213.75:
		windrose = "SSW"
	elif azimuth > 213.75 and azimuth < 236.25:
		windrose = "SW"
	elif azimuth > 236.25 and azimuth < 258.75:
		windrose = "WSW"
	elif azimuth > 258.75 and azimuth < 281.25:
		windrose = "W"
	elif azimuth > 281.25 and azimuth < 303.75:
		windrose = "WNW"
	elif azimuth > 303.75 and azimuth < 326.25:
		windrose = "NW"
	elif azimuth > 326.25 and azimuth < 348.75:
		windrose = "NNW"
	elif azimuth == 0 and windspeed == 0:
		windrose = "Calm"
	elif azimuth == 0 and windspeed != 0:
		windrose = "N"

	return windrose

def precipitation_convert(code):
	if (code == 0): # Конвертируем код типа осадков
		precipitation = "No precipitation"
	elif (code == 40):
		precipitation = "Unspecified precipitation"
	elif (code == 60):
		precipitation = "Rain"
	elif (code == 70):
		precipitation = "Snow"
	elif (code == 67):
		precipitation = "Freezing rain"
	elif (code == 69):
		precipitation = "Sleet"
	elif (code == 90):
		precipitation = "Hail"

	return precipitation

# function for communication with sensors and store received data
def logger():
	clearConsole = lambda: os.system('cls' if os.name in ('nt', 'dos') else 'clear')
	clearConsole() # clear previous data

	mydict = {} # create empty dictionary
	umb = WS_UMB(device='COM4') # create instance of WS_UMB class for COM4 port

	timestamp = datetime.datetime.now().strftime("%d-%m-%Y %H:%M:%S")
	mydict['DateTime'] = timestamp

	timestampUTC = datetime.datetime.utcnow().strftime("%d-%m-%Y %H:%M:%S")
	mydict['DateTimeUTC'] = timestampUTC

	temperature = round(umb.onlineDataQueryWS(2, 100)[0], 2)			# sampling rate 1min
	mydict['air_temp'] = temperature

	dewpoint = round(umb.onlineDataQueryWS(2, 110)[0], 2)			# sampling rate 1min
	mydict['dew_point'] = dewpoint

	relativehumidity = round(umb.onlineDataQueryWS(2, 200)[0], 2)	# sampling rate 1min
	mydict['humidity_rel'] = relativehumidity

	absolutehumidity = round(umb.onlineDataQueryWS(2, 205)[0], 2)	# sampling rate 1min
	mydict['humidity_abs'] = absolutehumidity

	humiditymixratio = round(umb.onlineDataQueryWS(2, 210)[0], 2)	# sampling rate 1min
	mydict['humidity_mixratio'] = humiditymixratio

	enthalpy = round(umb.onlineDataQueryWS(2, 215)[0], 2)	# sampling rate 1min
	mydict['spec_enthalpy'] = enthalpy

	airdensity = round(umb.onlineDataQueryWS(2, 310)[0], 2)	# sampling rate 1min
	mydict['air_density'] = airdensity

	absairpressure = round(umb.onlineDataQueryWS(2, 300)[0], 2)			# sampling rate 1min
	mydict['pressure_abs'] = absairpressure

	precipitationquantityabs = round(umb.onlineDataQueryWS(1, 620)[0], 2)	# sampling rate 10s
	mydict['precip_abs'] = precipitationquantityabs

	precipitationtype = round(umb.onlineDataQueryWS(1, 700)[0], 2)
	mydict['precip_code'] = precipitationtype

	precipitationintensity = round(umb.onlineDataQueryWS(1, 820)[0], 2)	# sampling rate 10s
	mydict['precip_intensity'] = precipitationintensity

	precipitation = precipitation_convert(precipitationtype)
	mydict['precip_name'] = precipitation

	globalradact = round(umb.onlineDataQueryWS(2, 900)[0], 2)  # sampling rate 10s
	mydict['globalrad_act'] = globalradact

	globalradmin = round(umb.onlineDataQueryWS(2, 920)[0], 2)  # sampling rate 10s
	mydict['globalrad_min'] = globalradmin

	globalradmax = round(umb.onlineDataQueryWS(2, 940)[0], 2)  # sampling rate 10s
	mydict['globalrad_max'] = globalradmax

	globalradmavg = round(umb.onlineDataQueryWS(2, 960)[0], 2)  # sampling rate 10s
	mydict['globalrad_avg'] = globalradmavg

	umb.close() # close connection for COM4

	umb2 = WS_UMB(device='COM3') # create instance of WS_UMB class for COM3 port

	windspeedact = round(umb2.onlineDataQueryVA(1, 400)[0], 2)	# sampling rate 10s
	mydict['windspeed_act'] = windspeedact

	windspeedmin = round(umb2.onlineDataQueryVA(1, 420)[0], 2)	# sampling rate 10s
	mydict['windspeed_min'] = windspeedmin

	windspeedmax = round(umb2.onlineDataQueryVA(1, 440)[0], 2)	# sampling rate 10s
	mydict['windspeed_max'] = windspeedmax

	windspeedavg = round(umb2.onlineDataQueryVA(1, 460)[0], 2)	# sampling rate 10s
	mydict['windspeed_avg'] = windspeedavg

	winddirectionact = round(umb2.onlineDataQueryVA(1, 500)[0], 2)  # sampling rate 10s
	mydict['winddir_act'] = winddirectionact

	winddirectionmin = round(umb2.onlineDataQueryVA(1, 520)[0], 2)  # sampling rate 10s
	mydict['winddir_min'] = winddirectionmin

	winddirectionmax = round(umb2.onlineDataQueryVA(1, 540)[0], 2)  # sampling rate 10s
	mydict['winddir_max'] = winddirectionmax

	windrose = wind_dir_convert(winddirectionact, windspeedact)  # sampling rate 10s
	mydict['windrose'] = windrose

	umb2.close() # close connection for COM3

	instrument = minimalmodbus.Instrument('COM4', 1)  # port name, slave address (in decimal)

	groundtempsurf_first = instrument.read_register(6, 1)
	if 6500 <= groundtempsurf_first <= 6554:
		temp_s = (6554.0 - groundtempsurf_first) * -1.0
		groundtempsurf = round(temp_s, 1)
		mydict['ground_temp_surface'] = groundtempsurf
	else:
		mydict['ground_temp_surface'] = groundtempsurf_first

	groundtemp_0_2 = instrument.read_register(4, 1)
	mydict['ground_temp_0_2'] = groundtemp_0_2

	groundtemp_0_4 = instrument.read_register(3, 1)
	mydict['ground_temp_0_4'] = groundtemp_0_4

	groundtemp_0_8 = instrument.read_register(2, 1)
	mydict['ground_temp_0_8'] = groundtemp_0_8

	groundtemp_1_2 = instrument.read_register(5, 1)
	mydict['ground_temp_1_2'] = groundtemp_1_2

	groundtemp_2_4 = instrument.read_register(1, 1)
	mydict['ground_temp_2_4'] = groundtemp_2_4

	instrument.serial.close()
	'''	
	with open('data.json') as f:
		data = json.load(f)

	print (json.dumps(mydict, sort_keys=True, indent=4, separators=(',', ': ')))
	data.append(mydict)

	with open('data.json', 'w') as f:
		json.dump(data, f, sort_keys=True, indent=4, separators=(',', ': '))
	'''
	print(mydict)

	try:
		writetodb = localcol.insert_one(mydict) # store dictionary to local database
	except Exception:
		print('Error! Data was not stored in local DB')
	else:
		print('Sucsess. Data stored to local DB')

	try:
		writetoremotedb = remotecol.insert_one(mydict) # store dictionary to remote database
	except Exception:
		print('Error! Data was not stored in remote DB')
	else:
		print('Sucsess. Data stored to remote DB')

# reset sensor function
def reset():
	umbreset = WS_UMB(device='COM4')

	reset_precipitation = umbreset.resetWSSensor(1)

	umbreset.close()


# schedule logging for every 10 minutes
schedule.every().hour.at(":00").do(logger)
schedule.every().hour.at(":10").do(logger)
schedule.every().hour.at(":20").do(logger)
schedule.every().hour.at(":30").do(logger)
schedule.every().hour.at(":40").do(logger)
schedule.every().hour.at(":50").do(logger)
# schedule precipitation reset every day at 23:59:00
schedule.every().day.at("23:59:00").do(reset)

# loop for communication with sensors and store received data

while True:
	#print("Meteo Logger Software. Running...")
	schedule.run_pending()
	time.sleep(1)

'''
while True:
	logger()
	print("Meteo Logger Software. Running...")
	time.sleep(30)
'''