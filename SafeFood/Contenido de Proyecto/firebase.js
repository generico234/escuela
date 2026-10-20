const firebaseConfig = {
  apiKey: "AIzaSyBPSumbPzFqi316bSr7Wy8S6ckBoEYj2Qw",
  authDomain: "safefoodweb.firebaseapp.com",
  projectId: "safefoodweb",
  storageBucket: "safefoodweb.firebasestorage.app",
  messagingSenderId: "697078968489",
  appId: "1:697078968489:web:92fd2ba5c3900c7e42f9b8",
  measurementId: "G-SGWD0RNXQ2"
};

import { initializeApp } from "https://www.gstatic.com/firebasejs/12.1.0/firebase-app.js";

const app = initializeApp(firebaseConfig);

console.log("🔥 Firebase conectado correctamente");