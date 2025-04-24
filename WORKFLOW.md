# Transaction Workflow (Mermaid)

Below is a Mermaid diagram illustrating the typical workflow of declarative transaction management and nested transaction propagation in this bundle:

```mermaid
sequenceDiagram
    participant Client
    participant Service as Transactional Service
    participant Aspect as TransactionalAspect
    participant DB as Database

    Client->>Service: Call public #[Transactional] method
    Service->>Aspect: Intercept via AOP (before)
    Aspect->>DB: Begin Transaction (if not active)
    Aspect->>Service: Proceed with method
    Service->>Aspect: (If nested #[Transactional])
    Aspect->>Aspect: Reuse existing transaction
    Service->>DB: DB operations (persist, flush, etc)
    Service->>Aspect: Return/Exception
    Aspect->>DB: Commit or Rollback
    Aspect->>Client: Return result
```

## 说明

- 只有外层事务会真正开启和提交/回滚事务，内层事务自动复用外层。
- 事务的开启、提交、回滚以及异常都会自动记录日志。
- 任何异常抛出时，事务自动回滚。
