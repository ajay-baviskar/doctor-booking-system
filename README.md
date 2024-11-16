# design patterns used in the Doctor Booking System project:
Controller-Service-Repository Pattern:

Controller handles HTTP requests.
Service contains business logic (e.g., appointment scheduling).
Repository abstracts data access (e.g., querying the database).
Repository Pattern: Abstracts data queries into repositories, decoupling the application from the database layer.

Singleton Pattern: Used in JWT authentication, where the JWTAuth class is a singleton, managing token creation and validation.

Factory Pattern: Used in JWT token generation, where JWTAuth::fromUser() creates a token object.

Strategy Pattern: Used for handling appointment status updates, where different strategies handle status changes based on the appointment’s state.

Validator Pattern: Custom request validation classes handle data validation separately from controllers, ensuring clean code.

Observer Pattern: Used for event-driven actions, such as notifying doctors or patients when an appointment is created, via Laravel’s event system.

Exception Handling: Custom error handling with try-catch blocks ensures consistent and meaningful error responses, like handling invalid tokens.

Dependency Injection: Classes like repositories are injected into controllers for better decoupling and easier testing.
