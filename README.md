# Turkpay Entegrasyonu

Bu branch Sadece turkpay modulü kullanan kullanıcılar için geçerlidir.

Entegrasyonun tamamlanması için  
  - Wsdl Güncellenmesi 
  ``` tappzBasketPurchaseCreditCardRequest  ``` methodunu güncellenmesi aşağıdaki gibi güncellenmesi gerekmektedir.
  ```sh
    <message name="tappzBasketPurchaseCreditCardRequest">
        <part name="sessionId" type="xsd:string"/>
        <part name="quoteId" type="xsd:string"/>
        <part name="payment" type="typens:payment"/>
    </message> 
  ```
  
  **Bu güncelleme sağlandıktan   wsdl  cache'inin silinmelidir.** 
  - Turkpay ödeme entegrasyonu 

  
  https://github.com/tappz/magento/blob/turkpay/app/code/community/TmobLabs/Tappz/Model/Basket/Api.php
  
  Linkte bulunan dosya  ile projenizde bulunan 
  ```sh
  app/code/community/TmobLabs/Tappz/Model/Basket/api.php 
  ```
  dosya ile değiştirmeniz gerekmektedir.
  
  - Api key oluşturulması 

Magnento admine giriş yaptıktan sonra 
 System > Configuration > Grinet turkpay > Genel Ayarlar > Web Servis Üzerinden Tahsilatı
Tıklayınız sonrasında 
 Webservis üzerinden tahsilat açık durumunu *YES* olarak seçip kendiniz bir api key oluşturmanız gerekmekte.
 Api key oluşturulduktan sonra daha önce değiştirdiğimiz```sh app/code/community/TmobLabs/Tappz/Model/Basket/api.php ```
  dosyasını açıp  ``` purchaseCreditCard ``` methodunun içinde bulunan api_key alanına daha önce oluşturmuş olduğunuz api key girmeniz gerekmekte.
  
  Bu işlemleri tamamladıktan sonra artık ödeme işlemi testleri yapabilir ve yayına alabilirsiniz.
  Herhangi bir problem yaşamanız durumunda support@t-appz.com mail atabilir.Buradan bize issue oluşturabilirsiniz.
  
  T-appz team 




  


