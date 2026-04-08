# RTO Database - Normalized Structure

## Database: `rto_new`

---

## Table 1: `person`
**Purpose:** Master table for personal information

| Column Name | Data Type | Constraints | Description |
|------------|-----------|-------------|-------------|
| person_id | INT(10) | PRIMARY KEY, AUTO_INCREMENT | Unique person identifier |
| aadhar | BIGINT(12) | UNIQUE, NOT NULL | Aadhar card number |
| name | VARCHAR(30) | NOT NULL | Person's full name |
| fatherName | VARCHAR(30) | NOT NULL | Father's name |
| dob | DATE | NOT NULL | Date of birth |
| bloodGroup | VARCHAR(3) | NOT NULL | Blood group (A+, B+, O+, etc.) |
| gender | VARCHAR(2) | NOT NULL | Gender (M/F) |
| address | TEXT | NOT NULL | Residential address |
| mobileNumber | BIGINT(13) | NOT NULL | Mobile number |
| email | VARCHAR(30) | NOT NULL | Email address |
| createdAt | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Record creation timestamp |

**Indexes:**
- PRIMARY KEY: `person_id`
- UNIQUE KEY: `aadhar`

---

## Table 2: `rtooffices`
**Purpose:** Reference table for RTO office information

| Column Name | Data Type | Constraints | Description |
|------------|-----------|-------------|-------------|
| rto_id | INT(10) | PRIMARY KEY, AUTO_INCREMENT | Unique RTO identifier |
| rtoCode | VARCHAR(10) | UNIQUE | RTO office code (e.g., DL, HR) |
| rtoName | VARCHAR(100) | NOT NULL | RTO office name |
| rtoAddress | TEXT | NOT NULL | RTO office address |
| state | VARCHAR(50) | NOT NULL | State name |

**Indexes:**
- PRIMARY KEY: `rto_id`
- UNIQUE KEY: `rtoCode`

---

## Table 3: `vehicleclasses`
**Purpose:** Reference table for vehicle classifications

| Column Name | Data Type | Constraints | Description |
|------------|-----------|-------------|-------------|
| class_id | INT(11) | PRIMARY KEY, AUTO_INCREMENT | Unique class identifier |
| classCode | VARCHAR(10) | NOT NULL | Vehicle class code |
| classDescription | VARCHAR(100) | | Full description of vehicle class |

**Indexes:**
- PRIMARY KEY: `class_id`

**Data:**
| class_id | classCode | classDescription |
|----------|-----------|------------------|
| 1 | MCWOG | Motorcycle Without Gear |
| 2 | MCWG | Motorcycle With Gear |
| 3 | LMV | Light Motor Vehicle |
| 4 | HMV | Heavy Motor Vehicle |
| 5 | TRANS | Transport Vehicle |

---

## Table 4: `licenses`
**Purpose:** Unified table for all license types (LL and DL)

| Column Name | Data Type | Constraints | Description |
|------------|-----------|-------------|-------------|
| license_id | INT(11) | PRIMARY KEY, AUTO_INCREMENT | Unique license identifier |
| licenseNumber | VARCHAR(20) | UNIQUE | License number (e.g., DL-123456) |
| person_id | INT(11) | FOREIGN KEY → person(person_id) | Reference to person |
| licenseType | ENUM('LL','DL') | | License type: LL or DL |
| class_id | INT(11) | FOREIGN KEY → vehicleclasses(class_id) | Vehicle class authorized |
| rto_id | INT(11) | FOREIGN KEY → rtooffices(rto_id) | Issuing RTO office |
| issueDate | DATE | | License issue date |
| examDate | DATE | | Exam date |
| validityDate | DATE | | License expiry date |
| status | ENUM('pending','approved','rejected','expired') | | Current license status |
| createdAt | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Record creation timestamp |

**Indexes:**
- PRIMARY KEY: `license_id`
- UNIQUE KEY: `licenseNumber`
- FOREIGN KEY: `person_id` → `person(person_id)`
- FOREIGN KEY: `rto_id` → `rtooffices(rto_id)`
- FOREIGN KEY: `class_id` → `vehicleclasses(class_id)`

---

## Entity Relationship Diagram (Text Format)

```
┌─────────────────┐
│   rtooffices    │
│  (Reference)    │
│                 │
│ PK: rto_id      │
│    rtoCode      │
│    rtoName      │
│    rtoAddress   │
│    state        │
└────────┬────────┘
         │
         │ (1:N)
         │
┌────────▼────────┐         ┌──────────────────┐
│    licenses     │         │ vehicleclasses   │
│                 │         │   (Reference)    │
│ PK: license_id  │         │                  │
│    licenseNumber│         │ PK: class_id     │
│ FK: person_id   │◄────┐   │    classCode     │
│    licenseType  │     │   │    classDesc     │
│ FK: class_id    │◄────┼───┤                  │
│ FK: rto_id      │     │   └──────────────────┘
│    issueDate    │     │
│    examDate     │     │
│    validityDate │     │
│    status       │     │ (1:N)
│    createdAt    │     │
└─────────────────┘     │
                        │
                        │
                ┌───────▼──────┐
                │    person    │
                │   (Master)   │
                │              │
                │ PK: person_id│
                │    aadhar    │
                │    name      │
                │    fatherName│
                │    dob       │
                │    bloodGroup│
                │    gender    │
                │    address   │
                │    mobile    │
                │    email     │
                │    createdAt │
                └──────────────┘
```

---

## Relationships

1. **person → licenses** (1:N)
   - One person can have multiple licenses (LL, then DL)

2. **rtooffices → licenses** (1:N)
   - One RTO office issues many licenses

3. **vehicleclasses → licenses** (1:N)
   - One vehicle class can be on many licenses

---

## Normalization Benefits

### Before (Old Structure):
-  `dl` and `ll` tables had duplicate structures
-  Personal information repeated in both tables
-  RTO stored as text strings
-  No referential integrity

### After (New Structure):
-  Single `licenses` table for all license types
-  Personal information stored once in `person` table
-  RTO offices normalized in reference table
-  Vehicle classes in reference table
-  Foreign key constraints ensure data integrity
-  No data redundancy
-  Easy to maintain and update

---

## Sample Query Examples

### Get complete license information:
```sql
SELECT 
    l.licenseNumber,
    l.licenseType,
    p.name,
    p.fatherName,
    p.dob,
    p.bloodGroup,
    vc.classCode,
    vc.classDescription,
    r.rtoName,
    l.issueDate,
    l.validityDate,
    l.status
FROM licenses l
JOIN person p ON l.person_id = p.person_id
JOIN vehicleclasses vc ON l.class_id = vc.class_id
JOIN rtooffices r ON l.rto_id = r.rto_id
WHERE l.licenseNumber = 'DL-123456';
```

### Get all licenses for a person:
```sql
SELECT 
    l.licenseNumber,
    l.licenseType,
    vc.classCode,
    l.status
FROM licenses l
JOIN person p ON l.person_id = p.person_id
JOIN vehicleclasses vc ON l.class_id = vc.class_id
WHERE p.aadhar = 1234567890;
```

---

## PHP Files That Need Updates

### Files to Update:
1.  `newDL.php` - Insert into new normalized tables
2.  `newLL.php` - Insert into new normalized tables
3.  `showdl.php` - Display using JOINs
4.  `showll.php` - Display using JOINs
5.  `checkDLStatus.php` - Query new structure
6.  `checkLLStatus.php` - Query new structure
7.  `admin/viewdlData.php` - Admin view with JOINs
8.  `admin/viewllData.php` - Admin view with JOINs
9.  `admin/editdldata.php` - Edit operations
10. `admin/editllData.php` - Edit operations

---

**Database Version:** rto_new  
**Last Updated:** April 8, 2026  
**Normalization Level:** 3NF (Third Normal Form)
