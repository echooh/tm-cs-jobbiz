# tm-cs-jobbiz
Platform managing job offers written with php/laravel
## Architecture
```mermaid
---
jobbiz architecture
---
erDiagram
    COMPANY ||--o{ JOB : belongs to
    COMPANY }|--|{ USER : has
    COMPANY {
        string name
        string address
    }
    JOB {
        string title
        string description
        int vacationDays
        float salary
    }  
    USER {
        string firstName
        string lastName
        date birthdate
        string gender
    }
```
