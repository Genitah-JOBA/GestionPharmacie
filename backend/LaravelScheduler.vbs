Set WshShell = CreateObject("WScript.Shell")
WshShell.Run "C:\xampp\php\php.exe C:\Users\JOBA\gestion_de_stocks_et_ventes_pharmaceutiques\backend\artisan schedule:run", 0, True
