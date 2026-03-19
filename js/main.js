// Main JavaScript for HTML Shikhi

document.addEventListener('DOMContentLoaded', function() {
  
  // Mobile menu toggle
  const menuToggle = document.querySelector('.mobile-menu-toggle');
  const navMenu = document.querySelector('.nav-menu');
  
  if (menuToggle) {
    menuToggle.addEventListener('click', function() {
      navMenu.classList.toggle('show');
      menuToggle.textContent = navMenu.classList.contains('show') ? '✕' : '☰';
    });
  }
  
  // Tab functionality for examples
  const tabButtons = document.querySelectorAll('.tab-button');
  
  tabButtons.forEach(button => {
    button.addEventListener('click', function() {
      // Remove active class from all tabs
      tabButtons.forEach(btn => btn.classList.remove('active'));
      
      // Add active class to clicked tab
      this.classList.add('active');
      
      // Here you would load different example content
      // This is simplified - in real implementation you'd fetch content
      const tabName = this.dataset.tab;
      loadExampleContent(tabName);
    });
  });
  
  // Code editor functionality
  const runButtons = document.querySelectorAll('.run-btn');
  
  runButtons.forEach(button => {
    button.addEventListener('click', function() {
      const editor = this.previousElementSibling;
      const resultDiv = this.nextElementSibling;
      
      if (editor && editor.classList.contains('code-editor') && resultDiv) {
        const code = editor.value;
        resultDiv.innerHTML = code;
        
        // Execute scripts if any
        const scripts = resultDiv.querySelectorAll('script');
        scripts.forEach(script => {
          const newScript = document.createElement('script');
          newScript.textContent = script.textContent;
          document.body.appendChild(newScript);
        });
      }
    });
  });
  
  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
      const href = this.getAttribute('href');
      if (href !== '#') {
        e.preventDefault();
        const target = document.querySelector(href);
        if (target) {
          target.scrollIntoView({ behavior: 'smooth' });
        }
      }
    });
  });
  
  // Active navigation highlighting based on scroll
  const sections = document.querySelectorAll('section[id]');
  
  window.addEventListener('scroll', function() {
    let current = '';
    const scrollY = window.scrollY;
    
    sections.forEach(section => {
      const sectionTop = section.offsetTop - 100;
      const sectionHeight = section.offsetHeight;
      
      if (scrollY >= sectionTop && scrollY < sectionTop + sectionHeight) {
        current = section.getAttribute('id');
      }
    });
    
    const navLinks = document.querySelectorAll('.sidebar-nav a');
    navLinks.forEach(link => {
      link.classList.remove('active');
      if (link.getAttribute('href') === `#${current}`) {
        link.classList.add('active');
      }
    });
  });
  
  // Load saved theme preference
  const savedTheme = localStorage.getItem('theme');
  if (savedTheme === 'dark') {
    document.body.classList.add('dark-theme');
  }
  
  // Search functionality
  const searchInput = document.querySelector('.search-box input');
  
  if (searchInput) {
    searchInput.addEventListener('keyup', function(e) {
      if (e.key === 'Enter') {
        const query = this.value.trim();
        if (query) {
          window.location.href = `/search?q=${encodeURIComponent(query)}`;
        }
      }
    });
  }
  
});

// Function to load example content
function loadExampleContent(tabName) {
  const codeDisplay = document.querySelector('.code-display pre');
  const previewDisplay = document.querySelector('.preview-display');
  
  if (!codeDisplay || !previewDisplay) return;
  
  const examples = {
    basic: {
      code: `<h1>এইচটিএমএল শিখছি</h1>\n<p>এটি একটি <strong>গুরুত্বপূর্ণ</strong> প্যারাগ্রাফ</p>\n<ul>\n  <li>প্রথম আইটেম</li>\n  <li>দ্বিতীয় আইটেম</li>\n</ul>`,
      preview: `<h1>এইচটিএমএল শিখছি</h1>
<p>এটি একটি <strong>গুরুত্বপূর্ণ</strong> প্যারাগ্রাফ</p>
<ul>
  <li>প্রথম আইটেম</li>
  <li>দ্বিতীয় আইটেম</li>
</ul>`
    },
    form: {
      code: `<form>\n  <label>নাম: <input type="text" name="name"></label><br>\n  <label>ইমেইল: <input type="email" name="email"></label><br>\n  <button type="submit">সাবমিট</button>\n</form>`,
      preview: `<form>
  <label>নাম: <input type="text" name="name"></label><br>
  <label>ইমেইল: <input type="email" name="email"></label><br>
  <button type="submit">সাবমিট</button>
</form>`
    },
    table: {
      code: `<table border="1">\n  <tr>\n    <th>নাম</th>\n    <th>বয়স</th>\n  </tr>\n  <tr>\n    <td>রহিম</td>\n    <td>২৫</td>\n  </tr>\n  <tr>\n    <td>করিম</td>\n    <td>৩০</td>\n  </tr>\n</table>`,
      preview: `<table border="1" style="border-collapse: collapse;">
  <tr><th>নাম</th><th>বয়স</th></tr>
  <tr><td>রহিম</td><td>২৫</td></tr>
  <tr><td>করিম</td><td>৩০</td></tr>
</table>`
    }
  };
  
  const example = examples[tabName] || examples.basic;
  codeDisplay.textContent = example.code;
  previewDisplay.innerHTML = example.preview;
}

// Function to copy code to clipboard
function copyCode(button) {
  const code = button.previousElementSibling.querySelector('code').textContent;
  navigator.clipboard.writeText(code).then(() => {
    button.textContent = 'কপি হয়েছে!';
    setTimeout(() => {
      button.textContent = 'কপি করুন';
    }, 2000);
  });
}

// Function to toggle answer visibility
function toggleAnswer(id) {
  const answer = document.getElementById(id);
  if (answer) {
    answer.classList.toggle('hidden');
  }
}

// Function to update progress
function updateProgress(topicId) {
  const progress = JSON.parse(localStorage.getItem('htmlShikhiProgress') || '{}');
  progress[topicId] = true;
  localStorage.setItem('htmlShikhiProgress', JSON.stringify(progress));
  
  // Update UI
  const topicLink = document.querySelector(`a[data-topic="${topicId}"]`);
  if (topicLink) {
    topicLink.classList.add('completed');
  }
}