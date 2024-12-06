

---

# 🌟 PHP User Query API 🌟  

🚀 This project provides developers with a reliable and flexible framework for integrating user data into their applications.  

## 📋 Overview  

The PHP User Query API retrieves detailed user information using **first name** and **last name**. Designed with modern PHP capabilities, this API offers a comprehensive feature set, from proxy configurations to session authentication. 🔒  

### Key Features  
- **User Information Query**: Fetch details like TCK, full name, date of birth, and father's name 🗂️  
- **Secure Queries**: Operates with cookie and session authentication mechanisms 🔑  
- **Proxy Support**: Adapts to various network configurations 🌍  
- **Easy Integration**: Quickly integrates into any PHP application ⚡  

---

## ⚙️ Requirements  
- PHP 7.4 or later 💻  
- `cURL` extension enabled 🌐  

---

## 🚀 Quick Start  

1. **Clone or Download the Repository**:  
   ```bash
   git clone https://github.com/finewiki/mariel.api.git
   ```  

2. **Set Up the Cookie File**:  
   Create a `cookie.txt` file containing cookie information and place it in the root directory of your project.  

3. **Make a Query**:  
   Send a POST request from your application to fetch user information:  
   ```bash
   POST /queryUser
   Content-Type: application/json

   {
     "first_name": "John",
     "last_name": "Doe"
   }
   ```  

4. **Access the Data and Enjoy!** 🎉  

---

## 🛠️ Example Usage  

The following example demonstrates how to use the API in a basic PHP application:  

```php
<?php
$url = 'https://api.example.com/queryUser';
$data = [
    'first_name' => 'John',
    'last_name' => 'Doe'
];

$options = [
    'http' => [
        'header'  => "Content-Type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($data),
    ],
];

$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);

echo $result;
?>
```  

---

## 💬 Contributing  

If you'd like to contribute to this project, please check out the [contribution guide](CONTRIBUTING.md).  
We welcome bug reports, feature suggestions, and contributions to enhance the project.  

---

## 📝 License  

This project is licensed under the MIT License. For more details, see the [LICENSE](LICENSE) file.  

---

### 🌟 A Simple and Effective Solution for Everyone!  

> Feel free to reach out with any questions or suggestions. Happy coding! 🖥️✨  

---
