��    h      \      �  f   �  i     e   m  (   �  +   �  *   (     S     b  k   y     �  j   �  #   f	  9   �	  '   �	     �	     
  -   
     B
     U
     m
  -   �
     �
     �
  *   �
  <   %  /   b     �     �  	   �     �     �        C        S  S   X  a   �  %     5   4     j  U     D   �  W     �   r  =   �  E   8  "   ~  <   �      �  u   �  �   u  Y   ,  �   �  +     r   J  q   �  7   /  y   g  Q   �     3  
   ;  $   F     k          �     �     �     �     �  !   �  &        (     B  B   \  8   �  p   �  E   I  9   �     �     �  U   �  |   B  ^   �  2     M   Q     �  E   �     �            H   "  h   k     �  /   �  	         (  
   I  4   T     �  <   �  6   �       f   $     �  Y   �  u     q   {  v   �  (   d  '   �  )   �     �     �  e        q  d   �     �  7        E     e     v  1   �     �  !   �     �  <      (   M   *   v   =   �   =   �   8   !  +   V!     �!     �!     �!     �!     �!  \   �!     9"  G   >"  p   �"  ,   �"  %   $#     J#  W   ]#  C   �#  w   �#  f   q$  ;   �$  E   %  *   Z%  3   �%  +   �%  b   �%  �   H&  c   �&  �   _'  6   (  �   8(  �   �(  A   ?)  �   �)  F   #*     j*     s*  $   �*     �*     �*     �*     �*     �*     �*  2   +     7+  (   T+     }+     �+  J   �+  9   ,  T   <,  >   �,  3   �,     -     -  m   .-  �   �-  j   9.  '   �.  P   �.     /  L   -/     z/     �/     �/  @   �/  d   �/     a0  5   ~0  
   �0     �0     �0  *   �0     1  ?   &1  4   f1     �1  ^   �1     2  0   12  This plugin enables scheduled backup of important part of your website : simple to use and efficient ! Sorry, but you should install/activate %s on your website. Otherwise, this plugin will not work properly! Check this option if you want to save everything. Be careful, because the backup could be quite huge! The login/password does not seems valid! The folder %s does not seems to be writable It seems impossible to switch to PASV mode Everything OK! The plugins directory: Check this option if you want to save all plugins that you have installed and that you use on this website. The themes directory: Check this option if you want to save all themes that you have installed and that you use on this website. The upload directory for this blog: All upload directories (for this site and the sub-blogs): The upload directory for the main site: The upload directory: The SQL database: How often do you want to backup your website? All SQL databases: Only your SQL database: The maximum file size (in MB): Do you want that the backup is sent by email? Send the backup files by email: If so, please enter your email: Do you want to add a suffix to sent files: You do not need to fill this field if no mail is to be sent. Do you want that the backup is stored on a FTP? Save the backup files on a FTP? Frequency (in days): FTP host: Should be at the form %s or %s Your FTP login: Your FTP pass: Click on that button %s to test if the above information is correct Test If you want to be notify when the FTP storage is finished, please enter your email: Your PHP installation does not support FTP features, thus this option has been disabled! Sorry... Advanced - Memory and time management What is the maximum size of allocated memory (in MB): Time of the backups: Please note that the files greater than this limit won't be included in the zip file! What is the maximum time for the php scripts execution (in seconds): Here is the backup files. You can force a new backup or download previous backup files. Please note that the current GMT time of the server is %s. If it is not correct, please configure the Wordpress installation correctly. An automatic backup will be launched in %s days and %s hours. The backup process has started %s hours ago but has not finished yet. Force a new backup (with Mail/FTP) Force a new backup (without any external storage or sending) How to restore the backup files? To restore the backups, and if you have backuped the full installation, you will have to execute the following steps: Please note that 0 means midnight, 1 means 1am, 13 means 1pm, etc. The backup will occur at that time (server time) so make sure that your website is not too overloaded at that time. Save all zip files (i.e. *.zip, *.z01, *.z02, etc.) in a single folder on your hard disk. Unzip these files by using IZArc, Winzip, or Winrar (others software could not support these multipart zip and consider that the archive is corrupted). Save the extracted files on your webserver. Reimport the SQL files (i.e. *.sql1, *sql2, etc.) with phpmyadmin (it is recommended to save your database first). To restore the backups, and if you have backuped only some folders, you will have to execute the following steps: Install a fresh version of Wordpress on your webserver. Replace the folders (i.e. 'plugins',  'themes', and/or 'uploads') of the root of your webserver by the extracted folders. Replace the wp-config.php (at the root of your webserver) with the extracted one. Backups Parameters Keep the backup files for (in days): Manage translations Give feedback Other plugins Date of the backup Backup files Part %s Backup finished on %s at %s The total size of the files is %s These files will be deleted in %s days Delete these backup files What do you want to save? The process is still in progress for this backup (begun %s at %s). The SQL extraction is in progress (%s tables extracted). The ZIP creation is in progress (%s files has been added in the zip file and the current size of the zip is %s). The FTP sending is in progress (%s files has been stored in the FTP). The MAIL sending is in progress (%s files has been sent). Cancel this process Please wait... (For now, there is no backup files... You should wait or force a backup (see below) ) Please wait, a backup is in progress! If you want to force a new backup, refresh this page and end the current backup first. It is impossible to create the %s file in the %s folder. Please check folder/file permissions. All directories (the full Wordpress installation): An other SQL extraction is still in progress (for %s seconds)... Please wait! (SQL extraction) An other backup is still in progress (for %s seconds)... Please wait! (ZIP creation) (FTP sending) (MAIL sending) Your Wordpress installation cannot send emails (with heavy attachments)! The file %s cannot be deleted. You should have a problem with file permissions or security restrictions. An unknown error occured! The process has been canceled by a third person (i.e. %s) A new backup has been generated! Dear sirs, Here is attached the %s on %s backup files for today Best regards, %s backup files has been successfully saved on your FTP (%s) To download them, please click on the following links: No host has been defined Your PHP installation does not support SSL features... Thus, please use a standard FTP and not a FTPS! The host %s cannot be resolved! The specified folder %s does not exists. Please create it so that the transfer may start! Bu eklenti web sitenizin önemli bir ihtiyacı olan zamanlanmış yedekleme sağlar : kullanımı kolay ve etkilidir! Üzgünüm, ama web sitenize %s kurup etkinleştirmelisiniz. Aksi halde, bu eklenti düzgün çalışmayacaktır. Her şeyi kaydetmek istiyorsanız bu seçeneği işaretleyin. Dikkatli olun, çünkü yedek oldukça büyük olabilir! Kullanıcı/Parola doğru görünmüyor! %s klasörü yazılabilir görünmüyor Pasif moda geçmek mümkün görünmüyor Herşey tamam! Eklentiler (plugins) dizini: Web sitenizde kullandığınız kurulu tüm eklentileri kaydetmek istiyorsanız bu seçeneği seçin. Temalar (themes) Dizini Web sitenizde kullandığınız  kurulu tüm temaları kaydetmek istiyorsanız bu seçeneği seçin. Bu blog için yükleme dizini: Tüm yükleme dizinleri (Bu site ve alt bloglar için): Ana site için yükleme dizini: Yükleme dizini: SQL Veritabanı: Ne sıklıkla web sitenizi yedeklemek istersiniz? Tüm SQL veritabanları: Sadece sizin SQL veritabanınız: Maksimum dosya boyutu (MB): Bu yedeğin e-posta yoluyla gönderilmesini istiyor musunuz? Yedek dosyalarını e-posta ile gönder: Öyleyse lütfen e-posta adresinizi girin: Size gönderilen dosyalara bir sonek eklemek istiyor musunuz: Mail istemiyorsanız bu alanı doldurmak zorunda değilsiniz. Bu yedeğin FTP yoluyla gönderilmesini istiyor musunuz? Yedekleme dosyalarını FTP&#039;ye kaydet? Sıklık (gün): FTP sunucu: Form %s veya %s olmalı FTP kullanıcı adı: FTP parolası: Yukarıdaki bilgilerin doğru olup olmadığını test etmek için %s düğmesine tıklayın Test FTP depolama bittiğinde haberdar olmak istiyorsanız, e-posta giriniz: PHP kurulumunuz FTP özellikleri desteklemiyor, bu nedenle bu seçenek devre dışı bırakıldı! Üzgünüm... Gelişmiş - Bellek (RAM) ve zaman yönetimi En fazla RAM bellek kullanımı (MB): Yedekleme zamanı: Dikkat edin, bu sınırdan daha büyük dosyaları zip dosyasına dahil edilmeyecektir! Php dosyalarının en fazla çalışma süresini belirtin (saniye): Yedek dosyalarınız buradadır. Yeni bir yedekleme başlatabilir veya önceki yedekleme dosyalarını indirebilirsiniz Sunucunun şu anki GMT zamanı %s. Bu doğru değilse, lütfen Wordpress ayarlarınızı kontrol edin. Bir otomatik yedekleme %s gün %s saat içinde başlayacak. Yedekleme işlemi %s saat önce başladı ancak henüz tamamlanmadı. Yeni bir yedekleme başlat (Posta/FTP ile) Yeni bir yedekleme başlat (Posta/FTP olmaksızın) Yedekleme dosyaları nasıl geri yüklenir? Yedekleri geri yükleme için ve Tam yedek aldıysanız aşağıdaki adımları takip etmelisiniz. 0&#039;ın anlamı gece yarısıdır. 1 Gece 1 demektir. 13 yazarsanız öğleden sonra 1 anlamına gelir. Yedekleme için ziyaretçi sayılarınızın en az olduğu saati seçin Tüm zip dosyalarını (örn: *.zip, *.z01, *.z02 gibi) bilgisayarınızdaki bir klasöre kaydedin. Winrar, Winzip, IZArc gibi bir programla bu dosyaları çıkarın. (Diğer yazılımlar çok parçalı zip dosyaları açmayı desteklemez ve arşivi bozabilir.) Çıkardığınız dosyaları web sunucunuza kaydedin. Phpmyadmin kullanarak SQL dosyalarınızı (örn: *.sql1, *sql2, gibi)  içe aktarın. (Veritabanını kaydetmek için bu önerilir.) Yedekleri geri yüklemek için ve eğer sadece bazı klasörleri yedeklediyseniz, aşağıdaki adımları takip etmeniz gerekir: Web sunucunuza Wordpress&#039;in yeni bir sürümünü yükleyin. Çıkartılan klasörleri (örn: &#039;plugins&#039;, &#039;themes&#039;, ve/veya &#039;uploads&#039;) web sunucunuzun ilgili klasörlerindekilerle değiştirin. wp-config.php dosyasını sunucunuzun ana dizinindekiyle değiştirin. Yedekler Parametreler Yedekleme dosyalarını koru (gün): Çevirileri yönet Görüşleriniz Diğer eklentiler Yedekleme tarihi Yedek dosyaları Part %s Yedekleme %s tarihinde saat %s&#039;te tamamlandı Dosyaların toplam boyutu %s Bu dosyalar %s gün içinde silinecektir Bu yedek dosyaları silin Neleri yedeklemek istiyorsunuz? Bu yedekleme işlemi devam etmektedir (%s tarihinde %s&#039;te başladı). SQL çıkarma devam ediyor (%s tabloları çıkarıldı). ZIP oluşturma devam ediyor (%s dosya eklendi ve dosyanın şu anki boyutu %s oldu). FTP gönderme devam ediyor (%s dosya FTP&#039;ye gönderildi). MAIL gönderme devam ediyor (%s dosya gönderildi). Bu işlemi iptal edin Lütfen bekleyin... (Şu anda yedek dosya yok... Bekleyebilir veya yeni bir yedekleme başlatabilirsiniz. (aşağıya bakınız)) Lütfen bekleyin, bir yedekleme işlemi devam ediyor! Yeni bir yedekleme başlatmak istiyorsanız önce bu sayfayı yenileyin ve mevcut yedeklemeyi bitirin. %s klasöründe %s dosyası oluşturmak mümkün değildir. Lütfen Klasör/Dosya izinlerini kontrol edin. Tüm dizinler (tam Wordpress kurulumu): Bir diğer SQL çıkarma (%s saniye) halen devam etmektedir... Lütfen bekleyin! (SQL çıkarma) Bir diğer yedekleme (%s saniye) halen devam etmektedir... Lütfen bekleyin! (ZIP oluşturma) (FTP&#039;ye gönderme) (Mail&#039;e gönderme) Wordpress kurulumunuz e-posta ile gönderilemez! (Büyük boyut) %s dosyası silinemedi. Dosya izinleri veya güvenlik kısıtlamaları ile ilgili bir sorun olmalı. Bilinmeyen bir hata oluştu! İşlem üçüncü bir kişi tarafından iptal edildi (örn: %s) Yeni bir yedek oluşturuldu! Sayın bay, İşte bugün için %s %s yedek dosyaları Saygılarımızla, %s yedekleme dosyaları başarıyla FTP&#039;ye (%s) kaydedildi İndirmek için aşağıdaki linklere tıklayınız: Hiç sunucu oluşturulmadı PHP kurulumunuz SSL özellikleri desteklemiyor... Lütfen standart FTP kullanın, FTPS değil! %s sunucusu çözülemedi! Belirtilen %s klasörü yok. Lütfen oluşturun! 