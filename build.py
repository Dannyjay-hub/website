import os
import datetime

SRC_DIR = "src"
OUTPUT_FILE = "index.html"

# Correct sequence of components to build index.html
COMPONENTS = [
    "meta.html",
    "sidebar.html",
    "banner.html",
    "about.html",
    "skills.html",
    "experience.html",
    "services.html",
    "projects.html",
    "partners.html",
    "contact.html",
    "footer.html"
]

def build():
    print("Starting build...")
    html_content = ""
    current_year = str(datetime.datetime.now().year)

    for comp in COMPONENTS:
        filepath = os.path.join(SRC_DIR, comp)
        if not os.path.exists(filepath):
            print(f"[ERROR] Component file {filepath} does not exist!")
            return False
        
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Add a newline between files to ensure separation
        html_content += content + "\n"
        print(f"Loaded {comp}")

    # Inject current year
    html_content = html_content.replace("{{YEAR}}", current_year)

    # Write compiled file
    with open(OUTPUT_FILE, 'w', encoding='utf-8') as f:
        f.write(html_content)
    
    print(f"Successfully compiled to {OUTPUT_FILE} ({len(html_content)} characters).")
    return True

if __name__ == "__main__":
    build()
