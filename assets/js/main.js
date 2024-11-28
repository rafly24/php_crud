// Login Handler
document.getElementById('loginForm')?.addEventListener('submit', async (e) => {
  e.preventDefault();
  const username = document.getElementById('username').value;
  const password = document.getElementById('password').value;

  try {
      const response = await fetch('api/auth/login.php', {
          method: 'POST',
          headers: {
              'Content-Type': 'application/json'
          },
          body: JSON.stringify({ username, password })
      });

      const data = await response.json();
      if (response.ok) {
          localStorage.setItem('user_id', data.user_id);
          window.location.href = 'index.html';
      } else {
          alert(data.message);
      }
  } catch (error) {
      console.error('Error:', error);
  }
});

// Todo List Handler
const loadTodos = async () => {
  const userId = localStorage.getItem('user_id');
  if (!userId) {
      window.location.href = 'login.html';
      return;
  }

  try {
      const response = await fetch(`api/tasks/read.php?user_id=${userId}`);
      const data = await response.json();
      
      const todoList = document.querySelector('.todo-list');
      todoList.innerHTML = '';
      
      data.forEach(todo => {
          const li = document.createElement('li');
          li.className = 'todo-item';
          li.innerHTML = `
              <span>${todo.title}</span>
              <div class="actions">
                  <button class="edit-btn" onclick="editTodo(${todo.id})">
                      <i class="fas fa-edit"></i>
                  </button>
                  <button class="delete-btn" onclick="deleteTodo(${todo.id})">
                      <i class="fas fa-trash"></i>
                  </button>
              </div>
          `;
          todoList.appendChild(li);
      });
  } catch (error) {
      console.error('Error:', error);
  }
};

// Add Todo
const addTodo = async (title) => {
  const userId = localStorage.getItem('user_id');
  try {
      const response = await fetch('api/tasks/create.php', {
          method: 'POST',
          headers: {
              'Content-Type': 'application/json'
          },
          body: JSON.stringify({ user_id: userId, title })
      });

      if (response.ok) {
          loadTodos();
      }
  } catch (error) {
      console.error('Error:', error);
  }
};

// Initialize
if (document.querySelector('.todo-container')) {
  loadTodos();
}
// Register form handler
document.getElementById('registerForm')?.addEventListener('submit', async (e) => {
  e.preventDefault();
  
  // Validasi password match
  const password = document.getElementById('password').value;
  const confirmPassword = document.getElementById('confirmPassword').value;
  
  if(password !== confirmPassword) {
      alert('Password tidak cocok!');
      return;
  }
  
  const formData = {
      first_name: document.getElementById('firstName').value,
      last_name: document.getElementById('lastName').value,
      email: document.getElementById('email').value,
      mobile: document.getElementById('mobile').value,
      username: document.getElementById('username').value,
      password: password
  };

  try {
      const response = await fetch('api/auth/register.php', {
          method: 'POST',
          headers: {
              'Content-Type': 'application/json'
          },
          body: JSON.stringify(formData)
      });

      const data = await response.json();
      
      if (response.ok) {
          alert('Registrasi berhasil! Silakan login.');
          window.location.href = 'login.html';
      } else {
          alert(data.message || 'Terjadi kesalahan saat registrasi.');
      }
  } catch (error) {
      console.error('Error:', error);
      alert('Terjadi kesalahan saat menghubungi server.');
  }
});

// Login form handler
document.getElementById('loginForm')?.addEventListener('submit', async (e) => {
  e.preventDefault();
  
  const formData = {
      username: document.getElementById('username').value,
      password: document.getElementById('password').value
  };

  try {
      const response = await fetch('api/auth/login.php', {
          method: 'POST',
          headers: {
              'Content-Type': 'application/json'
          },
          body: JSON.stringify(formData)
      });

      const data = await response.json();
      
      if (response.ok) {
          localStorage.setItem('user_id', data.user_id);
          localStorage.setItem('username', data.username);
          window.location.href = 'index.html';
      } else {
          alert(data.message || 'Username atau password salah.');
      }
  } catch (error) {
      console.error('Error:', error);
      alert('Terjadi kesalahan saat menghubungi server.');
  }
});